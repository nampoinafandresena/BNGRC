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
     * Insère une nouvelle ligne d'achat avec montant déjà calculé
     * @param int $id_ville
     * @param int $id_article
     * @param float $quantite
     * @param float $montant_argent_utilise Montant déjà calculé avec frais
     * @return bool True si succès, false sinon
     */
    public function saveAchat($id_ville, $id_article, $quantite, $montant_argent_utilise)
    {
        // Défini les propriétés et insère directement le montant fourni
        $this->setIdVille($id_ville)
            ->setIdArticle($id_article)
            ->setQuantite($quantite)
            ->setMontantArgentUtilise($montant_argent_utilise);

        return $this->create();
    }

    // get tous les achats avec les tous les details 
    public static function getAllWithDetails($db, $id_ville = null) {
        $sql = "SELECT ac.*, v.nom as nom_ville, a.label as nom_article, a.prix_unitaire 
                FROM BNGRC_achat ac
                JOIN BNGRC_ville v ON ac.id_ville = v.id
                JOIN BNGRC_article a ON ac.id_article = a.id";
        
        if ($id_ville) {
            $sql .= " WHERE ac.id_ville = :id_ville";
        }
        
        $sql .= " ORDER BY ac.date_achat DESC";
        
        $stmt = $db->prepare($sql);
        if ($id_ville) {
            $stmt->execute(['id_ville' => $id_ville]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // statistiques des achats
    public static function getStats($db)
    {
        $query = $db->prepare("
            SELECT 
                COUNT(*) as total_achats,
                SUM(quantite) as quantite_totale,
                SUM(montant_argent_utilise) as montant_total,
                AVG(montant_argent_utilise / quantite) as prix_moyen_unitaire
            FROM BNGRC_achat
        ");
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // get achats par periodes
    public static function getByPeriode($db, $date_debut, $date_fin)
    {
        $query = $db->prepare("
            SELECT a.*, v.nom_ville, art.nom_article 
            FROM BNGRC_achat a
            LEFT JOIN BNGRC_ville v ON a.id_ville = v.id
            LEFT JOIN BNGRC_article art ON a.id_article = art.id
            WHERE DATE(a.date_achat) BETWEEN :date_debut AND :date_fin
            ORDER BY a.date_achat DESC
        ");
        $query->execute([
            ':date_debut' => $date_debut,
            ':date_fin' => $date_fin
        ]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByArticle($db, $id_article)
    {
        $query = $db->prepare("
            SELECT a.*, v.nom_ville 
            FROM BNGRC_achat a
            LEFT JOIN BNGRC_ville v ON a.id_ville = v.id
            WHERE a.id_article = :id_article
            ORDER BY a.date_achat DESC
        ");
        $query->execute([':id_article' => $id_article]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getByVille($db, $id_ville)
    {
        $query = $db->prepare("
            SELECT a.*, art.nom_article 
            FROM BNGRC_achat a
            LEFT JOIN BNGRC_article art ON a.id_article = art.id
            WHERE a.id_ville = :id_ville
            ORDER BY a.date_achat DESC
        ");
        $query->execute([':id_ville' => $id_ville]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifie si un achat peut être modifié/supprimé
    public function isModifiable()
    {
        // Exemple de règle : un achat de plus de 30 jours ne peut pas être modifié
        $dateAchat = new \DateTime($this->date_achat);
        $dateLimite = new \DateTime('-30 days');
        
        return $dateAchat >= $dateLimite;
    }

    // Calcule le montant HT d'un achat
     public function calculerMontantHT($prix_unitaire)
    {
        return $this->quantite * $prix_unitaire;
    }

    public function calculerMontantTTC($prix_unitaire, $frais_pourcentage)
    {
        $montant_ht = $this->calculerMontantHT($prix_unitaire);
        $montant_frais = $montant_ht * $frais_pourcentage;
        return $montant_ht + $montant_frais;
    }

    //  Récupère le montant total des achats pour un article
     
    public static function getMontantTotalByArticle($db, $id_article)
    {
        $query = $db->prepare("
            SELECT SUM(montant_argent_utilise) as total 
            FROM BNGRC_achat 
            WHERE id_article = :id_article
        ");
        $query->execute([':id_article' => $id_article]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

}
