<?php

namespace app\models;

use PDO;

class Achat
{
    public $db;
    public $id;
    public $id_ville;
    public $id_article;
    public $quantite;
    public $montant_argent_utilise;
    public $date_achat;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIdVille()
    {
        return $this->id_ville;
    }

    public function getIdArticle()
    {
        return $this->id_article;
    }

    public function getQuantite()
    {
        return $this->quantite;
    }

    public function getMontantArgentUtilise()
    {
        return $this->montant_argent_utilise;
    }

    public function getDateAchat()
    {
        return $this->date_achat;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setIdVille($id_ville)
    {
        $this->id_ville = $id_ville;
        return $this;
    }

    public function setIdArticle($id_article)
    {
        $this->id_article = $id_article;
        return $this;
    }

    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function setMontantArgentUtilise($montant_argent_utilise)
    {
        $this->montant_argent_utilise = $montant_argent_utilise;
        return $this;
    }

    public function setDateAchat($date_achat)
    {
        $this->date_achat = $date_achat;
        return $this;
    }

    public function create()
    {
        $query = $this->db->prepare(
            "INSERT INTO BNGRC_achat (id_ville, id_article, quantite, montant_argent_utilise) 
             VALUES (:id_ville, :id_article, :quantite, :montant_argent_utilise)"
        );

        if ($query->execute([
            ':id_ville' => $this->id_ville,
            ':id_article' => $this->id_article,
            ':quantite' => $this->quantite,
            ':montant_argent_utilise' => $this->montant_argent_utilise
        ])) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    public function read($id)
    {
        $query = $this->db->prepare("SELECT * FROM BNGRC_achat WHERE id = :id");
        $query->execute([':id' => $id]);

        $data = $query->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            $this->id = $data['id'];
            $this->id_ville = $data['id_ville'];
            $this->id_article = $data['id_article'];
            $this->quantite = $data['quantite'];
            $this->montant_argent_utilise = $data['montant_argent_utilise'];
            $this->date_achat = $data['date_achat'];
            return $this;
        }
        return null;
    }

    public static function readAll($db)
    {
        $query = $db->prepare("SELECT * FROM BNGRC_achat ORDER BY date_achat DESC");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        $query = $this->db->prepare(
            "UPDATE BNGRC_achat SET id_ville = :id_ville, id_article = :id_article, 
             quantite = :quantite, montant_argent_utilise = :montant_argent_utilise WHERE id = :id"
        );

        return $query->execute([
            ':id' => $this->id,
            ':id_ville' => $this->id_ville,
            ':id_article' => $this->id_article,
            ':quantite' => $this->quantite,
            ':montant_argent_utilise' => $this->montant_argent_utilise
        ]);
    }

    public function delete($id)
    {
        $query = $this->db->prepare("DELETE FROM BNGRC_achat WHERE id = :id");
        return $query->execute([':id' => $id]);
    }

    /**
     * Récupère la valeur des frais d'achat depuis BNGRC_config
     * @return float|null La valeur des frais_achat ou null si non trouvée
     */
    public function getFraisConfig()
    {
        $query = $this->db->prepare("SELECT frais_achat FROM BNGRC_config LIMIT 1");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['frais_achat'] : null;
    }

    /**
     * Calcule le montant de dons restant (non distribués) pour un article
     * @param int $id_article ID de l'article
     * @return float Le montant restant (Dons reçus - Distributions faites)
     */
    public function getDonRestant($id_article)
    {
        // Calcule le total des dons reçus pour cet article
        $donQuery = $this->db->prepare(
            "SELECT SUM(quantite_recue) as total_dons FROM BNGRC_don_collecte WHERE id_article = :id_article"
        );
        $donQuery->execute([':id_article' => $id_article]);
        $donResult = $donQuery->fetch(PDO::FETCH_ASSOC);
        $totalDons = $donResult['total_dons'] ?? 0;

        // Calcule le total des distributions faites pour les dons de cet article
        $distQuery = $this->db->prepare(
            "SELECT SUM(d.quantite_attribuee) as total_distributions FROM BNGRC_distribution d
             INNER JOIN BNGRC_don_collecte dc ON d.id_don = dc.id
             WHERE dc.id_article = :id_article"
        );
        $distQuery->execute([':id_article' => $id_article]);
        $distResult = $distQuery->fetch(PDO::FETCH_ASSOC);
        $totalDistributions = $distResult['total_distributions'] ?? 0;

        return $totalDons - $totalDistributions;
    }

    /**
     * Insère une nouvelle ligne d'achat avec calcul automatique du montant total
     * @param int $id_ville
     * @param int $id_article
     * @param float $quantite
     * @param float $prix_unitaire Prix unitaire de l'article
     * @return bool True si succès, false sinon
     */
    public function saveAchat($id_ville, $id_article, $quantite, $prix_unitaire)
    {
        // Récupère les frais d'achat en pourcentage
        $frais_pourcentage = $this->getFraisConfig();
        
        if ($frais_pourcentage === null) {
            return false; // Configuration des frais non trouvée
        }

        // Calcule le montant total TTC
        $montant_ht = $quantite * $prix_unitaire;
        $montant_frais = $montant_ht * $frais_pourcentage;
        $montant_ttc = $montant_ht + $montant_frais;

        // Défini les propriétés et insère
        $this->setIdVille($id_ville)
            ->setIdArticle($id_article)
            ->setQuantite($quantite)
            ->setMontantArgentUtilise($montant_ttc);

        return $this->create();
    }
}
