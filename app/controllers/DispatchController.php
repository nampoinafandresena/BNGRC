<?php

namespace app\controllers;

use flight;
use flight\Engine;
use app\models\DonCollecte;
use app\models\BesoinVille;
use app\models\Distribution;

class DispatchController
{

    /*
     * ============================================
     * ANCIENNES FONCTIONNALITÉS 
     * ============================================
     * 
     * L'ancienne méthode simulateDispatch() sauvegardait directement les distributions.
     * Elle a été refactorisée en deux méthodes séparées :
     * - simulateDispatchPreview() : retourne les propositions SANS sauvegarder
     * - validateDispatch() : persiste vraiment les distributions
     * 
     * Voici l'ancienne implémentation pour référence :
     *
     * public function simulateDispatch()
     * {
     *     $resumeDispatch = [
     *         'dons_traites' => 0,
     *         'attributions_creees' => 0,
     *         'quantite_totale_attribuee' => 0,
     *         'erreurs' => []
     *     ];
     *
     *     // 1. Récupérer tous les dons non épuisés, par ordre de date croissante (FIFO)
     *     $donCollecte = new DonCollecte(Flight::db());
     *     $tousDons = $donCollecte->readAll();
     *     $tousDons = array_reverse($tousDons);
     *
     *     // 2. Pour chaque don
     *     foreach ($tousDons as $don) {
     *         $idArticle = $don['id_article'];
     *         $quantiteDisponible = $don['quantite_recue'] - $this->getQuantiteDistribuee($don['id']);
     *
     *         if ($quantiteDisponible <= 0) {
     *             continue;
     *         }
     *
     *         $resumeDispatch['dons_traites']++;
     *
     *         // 3. Chercher les besoins du même article
     *         $besoinsArticle = $this->getBesoinsNonSatisfaits($idArticle);
     *
     *         // 4. Attribuer le don en boucle jusqu'à épuisement
     *         foreach ($besoinsArticle as $besoin) {
     *             $quantiteManquante = $besoin['quantite_demandee'] - $this->getQuantiteAttribuee($besoin['id']);
     *
     *             if ($quantiteManquante <= 0) {
     *                 continue;
     *             }
     *
     *             $quantiteAAttribuer = min($quantiteDisponible, $quantiteManquante);
     *
     *             // Créer la distribution (sauvegarde directe)
     *             $distribution = new Distribution(Flight::db());
     *             $distribution
     *                 ->setIdDon($don['id'])
     *                 ->setIdBesoinVille($besoin['id'])
     *                 ->setQuantiteAttribuee($quantiteAAttribuer);
     *
     *             if ($distribution->create()) {
     *                 $resumeDispatch['attributions_creees']++;
     *                 $resumeDispatch['quantite_totale_attribuee'] += $quantiteAAttribuer;
     *                 $quantiteDisponible -= $quantiteAAttribuer;
     *
     *                 if ($quantiteDisponible <= 0) {
     *                     break;
     *                 }
     *             } else {
     *                 $resumeDispatch['erreurs'][] = "Erreur lors de la création...";
     *             }
     *         }
     *     }
     *
     *     return $resumeDispatch;
     * }
     * ============================================
     */

    
    public function simulateDispatchByMinimum()
    {
        $propositions = [];
        $stats = [
            'dons_traites' => 0,
            'attributions_proposees' => 0,
            'quantite_totale_proposee' => 0
        ];

        $db = flight::db();

        // 1. Récupérer tous les besoins non satisfaits, triés par reste croissant (minimum en premier)
        $query = $db->prepare(
            "SELECT 
                bv.id as id_besoin_ville,
                bv.id_ville,
                bv.id_article,
                bv.quantite_demandee,
                a.prix_unitaire,
                a.label as article_label,
                v.nom as ville_nom,
                (bv.quantite_demandee - COALESCE(SUM(d.quantite_attribuee), 0)) as reste
            FROM BNGRC_besoin_ville bv
            JOIN BNGRC_article a ON bv.id_article = a.id
            JOIN BNGRC_ville v ON bv.id_ville = v.id
            LEFT JOIN BNGRC_distribution d ON d.id_besoin_ville = bv.id
            GROUP BY bv.id
            HAVING reste > 0
            ORDER BY reste ASC"
        );
        $query->execute();
        $besoinsNonSatisfaits = $query->fetchAll(\PDO::FETCH_ASSOC);

        // 2. Pour chaque besoin (trié par minimum), chercher des dons disponibles
        foreach ($besoinsNonSatisfaits as $besoin) {
            $idArticle = $besoin['id_article'];
            $quantiteManquante = $besoin['reste'];

            if ($quantiteManquante <= 0) {
                continue;
            }

            // Chercher les dons disponibles pour cet article
            $queryDons = $db->prepare(
                "SELECT 
                    dc.id,
                    dc.quantite_recue,
                    dc.donateur,
                    (dc.quantite_recue - COALESCE(SUM(d.quantite_attribuee), 0)) as quantite_disponible
                FROM BNGRC_don_collecte dc
                LEFT JOIN BNGRC_distribution d ON d.id_don = dc.id
                WHERE dc.id_article = :id_article
                GROUP BY dc.id
                HAVING quantite_disponible > 0"
            );
            $queryDons->execute([':id_article' => $idArticle]);
            $donsDisponibles = $queryDons->fetchAll(\PDO::FETCH_ASSOC);

            // 3. Attribuer les dons au besoin
            foreach ($donsDisponibles as $don) {
                if ($quantiteManquante <= 0) {
                    break;
                }

                $quantiteAAttribuer = min($don['quantite_disponible'], $quantiteManquante);

                $propositions[] = [
                    'id_don' => $don['id'],
                    'id_article' => $idArticle,
                    'id_besoin_ville' => $besoin['id_besoin_ville'],
                    'quantite_attribuee' => $quantiteAAttribuer,
                    'donateur' => $don['donateur'],
                    'ville_id' => $besoin['id_ville'],
                    'article_label' => $besoin['article_label'],
                    'ville_nom' => $besoin['ville_nom'],
                    'reste_initial' => $besoin['reste']
                ];

                $stats['attributions_proposees']++;
                $stats['quantite_totale_proposee'] += $quantiteAAttribuer;
                $quantiteManquante -= $quantiteAAttribuer;
            }
        }

        $stats['dons_traites'] = count(array_unique(array_column($propositions, 'id_don')));

        return [
            'propositions' => $propositions,
            'stats' => $stats
        ];
    }

    /**
     * Récupère l'état global des villes AVEC les propositions appliquées (simulation)
     * Utilisé pour mettre à jour l'affichage sans sauvegarder
     */
    public function getSimulatedState($propositions = [])
    {
        $db = flight::db();

        // État initial depuis la base
        $query = $db->prepare(
            "SELECT 
                bv.id as id_besoin_ville,
                v.id as id_ville,
                v.nom AS ville_nom, 
                r.nom AS region_nom,
                a.id AS id_article,
                a.label AS article_label, 
                a.prix_unitaire,
                bv.quantite_demandee,
                COALESCE(SUM(d.quantite_attribuee), 0) AS quantite_recue,
                (bv.quantite_demandee - COALESCE(SUM(d.quantite_attribuee), 0)) AS reste
            FROM BNGRC_besoin_ville bv
            JOIN BNGRC_ville v ON bv.id_ville = v.id
            JOIN BNGRC_region r ON v.id_region = r.id
            JOIN BNGRC_article a ON bv.id_article = a.id
            LEFT JOIN BNGRC_distribution d ON d.id_besoin_ville = bv.id
            GROUP BY bv.id
            ORDER BY r.nom, v.nom ASC"
        );
        $query->execute();
        $stats = $query->fetchAll(\PDO::FETCH_ASSOC);

        // Appliquer les propositions de simulation sur les données
        foreach ($propositions as $prop) {
            foreach ($stats as &$stat) {
                if ($stat['id_besoin_ville'] == $prop['id_besoin_ville']) {
                    // Augmenter la quantité reçue
                    $stat['quantite_recue'] += $prop['quantite_attribuee'];
                    // Recalculer le reste
                    $stat['reste'] = max(0, $stat['quantite_demandee'] - $stat['quantite_recue']);
                    break;
                }
            }
        }

        return $stats;
    }

    /**
     * Route AJAX : retourne état simulé + propositions (pour minimum dispatch)
     */
    public function previewMinimumDispatch()
    {
        $result = $this->simulateDispatchByMinimum();
        $propositions = $result['propositions'];
        $stats = $result['stats'];

        // Récupérer l'état simulé des villes
        $simulatedStats = $this->getSimulatedState($propositions);

        return [
            'propositions' => $propositions,
            'stats' => $stats,
            'simulated_state' => $simulatedStats
        ];
    }

    /**
     * Valide et sauvegarde les propositions du dispatch minimum
     */
    public function validateMinimumDispatch()
    {
        $result = [
            'success' => false,
            'attributions_creees' => 0,
            'quantite_totale_attribuee' => 0,
            'erreurs' => []
        ];

        $db = flight::db();

        // Récupérer les propositions actuelles (recalculer)
        $dispatchResult = $this->simulateDispatchByMinimum();
        $propositions = $dispatchResult['propositions'];

        // Sauvegarder chaque proposition
        foreach ($propositions as $prop) {
            $distribution = new Distribution($db);
            $distribution
                ->setIdDon($prop['id_don'])
                ->setIdBesoinVille($prop['id_besoin_ville'])
                ->setQuantiteAttribuee($prop['quantite_attribuee']);

            if ($distribution->create()) {
                $result['attributions_creees']++;
                $result['quantite_totale_attribuee'] += $prop['quantite_attribuee'];
            } else {
                $result['erreurs'][] = "Erreur lors de la création pour don #{$prop['id_don']}";
            }
        }

        $result['success'] = count($result['erreurs']) === 0;
        return $result;
    }
    
    /**
     * Simule et retourne les distributions proposées SANS les sauvegarder (Preview)
     */
    public function simulateDispatchPreview()
    {
        return $this->getProposedDistributions();
    }

    /**
     * Valide et sauvegarde les distributions proposées (Persist)
     */
    public function validateDispatch()
    {
        $result = [
            'success' => false,
            'attributions_creees' => 0,
            'quantite_totale_attribuee' => 0,
            'erreurs' => []
        ];

        // 1. Récupérer tous les dons non épuisés, par ordre de date croissante (FIFO)
        $donCollecte = new DonCollecte(Flight::db());
        $tousDons = $donCollecte->readAll();
        $tousDons = array_reverse($tousDons);

        // 2. Pour chaque don, créer et sauvegarder les distributions
        foreach ($tousDons as $don) {
            $idArticle = $don['id_article'];
            $quantiteDisponible = $don['quantite_recue'] - $this->getQuantiteDistribuee($don['id']);

            if ($quantiteDisponible <= 0) {
                continue;
            }

            // 3. Chercher les besoins du même article
            $besoinsArticle = $this->getBesoinsNonSatisfaits($idArticle);

            // 4. Attribuer le don en boucle jusqu'à épuisement
            foreach ($besoinsArticle as $besoin) {
                $quantiteManquante = $besoin['quantite_demandee'] - $this->getQuantiteAttribuee($besoin['id']);

                if ($quantiteManquante <= 0) {
                    continue;
                }

                $quantiteAAttribuer = min($quantiteDisponible, $quantiteManquante);

                // Créer et sauvegarder la distribution
                $distribution = new Distribution(Flight::db());
                $distribution
                    ->setIdDon($don['id'])
                    ->setIdBesoinVille($besoin['id'])
                    ->setQuantiteAttribuee($quantiteAAttribuer);

                if ($distribution->create()) {
                    $result['attributions_creees']++;
                    $result['quantite_totale_attribuee'] += $quantiteAAttribuer;
                    $quantiteDisponible -= $quantiteAAttribuer;

                    if ($quantiteDisponible <= 0) {
                        break;
                    }
                } else {
                    $result['erreurs'][] = "Erreur lors de la création de la distribution pour le don #{$don['id']} et le besoin #{$besoin['id']}";
                }
            }
        }

        $result['success'] = count($result['erreurs']) === 0;
        return $result;
    }

    /**
     * Calcule les distributions proposées SANS les sauvegarder
     */
    private function getProposedDistributions()
    {
        $propositions = [];
        $stats = [
            'dons_traites' => 0,
            'attributions_proposees' => 0,
            'quantite_totale_proposee' => 0
        ];

        // 1. Récupérer tous les dons non épuisés
        $donCollecte = new DonCollecte(Flight::db());
        $tousDons = $donCollecte->readAll();
        $tousDons = array_reverse($tousDons);

        // 2. Pour chaque don, proposer des attributions (SANS sauvegarder)
        foreach ($tousDons as $don) {
            $idArticle = $don['id_article'];
            $quantiteDisponible = $don['quantite_recue'] - $this->getQuantiteDistribuee($don['id']);

            if ($quantiteDisponible <= 0) {
                continue;
            }

            $stats['dons_traites']++;

            // 3. Chercher les besoins du même article
            $besoinsArticle = $this->getBesoinsNonSatisfaits($idArticle);

            // 4. Proposer l'attribution du don en boucle
            foreach ($besoinsArticle as $besoin) {
                $quantiteManquante = $besoin['quantite_demandee'] - $this->getQuantiteAttribuee($besoin['id']);

                if ($quantiteManquante <= 0) {
                    continue;
                }

                $quantiteAAttribuer = min($quantiteDisponible, $quantiteManquante);

                // Ajouter la proposition (sans sauvegarder)
                $propositions[] = [
                    'id_don' => $don['id'],
                    'id_article' => $idArticle,
                    'id_besoin_ville' => $besoin['id'],
                    'quantite_attribuee' => $quantiteAAttribuer,
                    'donateur' => $don['donateur'],
                    'ville_id' => $besoin['id_ville']
                ];

                $stats['attributions_proposees']++;
                $stats['quantite_totale_proposee'] += $quantiteAAttribuer;
                $quantiteDisponible -= $quantiteAAttribuer;

                if ($quantiteDisponible <= 0) {
                    break;
                }
            }
        }

        return [
            'propositions' => $propositions,
            'stats' => $stats
        ];
    }

    
    private function getBesoinsNonSatisfaits($idArticle)
    {
        $query = Flight::db()->prepare(
            "SELECT bv.* 
             FROM BNGRC_besoin_ville bv
             WHERE bv.id_article = :id_article
             AND bv.quantite_demandee > COALESCE(
                (SELECT SUM(quantite_attribuee) FROM BNGRC_distribution WHERE id_besoin_ville = bv.id), 0
             )
             ORDER BY bv.date_demande ASC"
        );
        $query->execute([':id_article' => $idArticle]);
        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    
    private function getQuantiteDistribuee($idDon)
    {
        $query = Flight::db()->prepare(
            "SELECT COALESCE(SUM(quantite_attribuee), 0) as total 
             FROM BNGRC_distribution 
             WHERE id_don = :id_don"
        );
        $query->execute([':id_don' => $idDon]);
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return (float) ($result['total'] ?? 0);
    }

   
    private function getQuantiteAttribuee($idBesoin)
    {
        $query = Flight::db()->prepare(
            "SELECT COALESCE(SUM(quantite_attribuee), 0) as total 
             FROM BNGRC_distribution 
             WHERE id_besoin_ville = :id_besoin"
        );
        $query->execute([':id_besoin' => $idBesoin]);
        $result = $query->fetch(\PDO::FETCH_ASSOC);
        return (float) ($result['total'] ?? 0);
    }
}
?>
