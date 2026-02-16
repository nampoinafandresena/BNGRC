<?php

namespace app\controllers;

use flight\Engine;
use app\models\DonCollecte;
use app\models\BesoinVille;
use app\models\Distribution;

class DispatchController
{
    protected Engine $app;
    protected $db;

    public function __construct($app)
    {
        $this->app = $app;
        $this->db = $app->db();
    }

   
    public function simulateDispatch()
    {
        $resumeDispatch = [
            'dons_traites' => 0,
            'attributions_creees' => 0,
            'quantite_totale_attribuee' => 0,
            'erreurs' => []
        ];

        // 1. Récupérer tous les dons non épuisés, par ordre de date croissante (FIFO)
        $donCollecte = new DonCollecte($this->db);
        $tousDons = $donCollecte->readAll(); // Cette méthode retourne par date DESC, on va inverser

        // Inverser pour avoir l'ordre croissant (plus ancien en premier)
        $tousDons = array_reverse($tousDons);

        // 2. Pour chaque don
        foreach ($tousDons as $don) {
            $idArticle = $don['id_article'];
            $quantiteDisponible = $don['quantite_recue'] - $this->getQuantiteDistribuee($don['id']);

            // Si le don est déjà complètement distribué, passer au suivant
            if ($quantiteDisponible <= 0) {
                continue;
            }

            $resumeDispatch['dons_traites']++;

            // 3. Chercher les besoins du même article (ordre date croissante)
            $besoinsArticle = $this->getBesoinsNonSatisfaits($idArticle);

            // 4. Attribuer le don en boucle jusqu'à épuisement
            foreach ($besoinsArticle as $besoin) {
                // Quantité manquante pour ce besoin
                $quantiteManquante = $besoin['quantite_demandee'] - $this->getQuantiteAttribuee($besoin['id']);

                if ($quantiteManquante <= 0) {
                    // Ce besoin est déjà satisfait
                    continue;
                }

                // Quantité à attribuer = minimum entre ce qu'il reste du don et ce qui manque au besoin
                $quantiteAAttribuer = min($quantiteDisponible, $quantiteManquante);

                // Créer la distribution
                $distribution = new Distribution($this->db);
                $distribution
                    ->setIdDon($don['id'])
                    ->setIdBesoinVille($besoin['id'])
                    ->setQuantiteAttribuee($quantiteAAttribuer);

                if ($distribution->create()) {
                    $resumeDispatch['attributions_creees']++;
                    $resumeDispatch['quantite_totale_attribuee'] += $quantiteAAttribuer;
                    $quantiteDisponible -= $quantiteAAttribuer;

                    // Si le don est épuisé, passer au don suivant
                    if ($quantiteDisponible <= 0) {
                        break;
                    }
                } else {
                    $resumeDispatch['erreurs'][] = "Erreur lors de la création de la distribution pour le don #{$don['id']} et le besoin #{$besoin['id']}";
                }
            }
        }

        return $resumeDispatch;
    }

    
    private function getBesoinsNonSatisfaits($idArticle)
    {
        $query = $this->db->prepare(
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
        $query = $this->db->prepare(
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
        $query = $this->db->prepare(
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
