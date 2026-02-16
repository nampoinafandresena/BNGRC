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
