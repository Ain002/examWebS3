<?php

namespace app\models;

use flight;

class BesoinSatisfaitModel {

    public $id;
    public $idBesoin;
    public $dateSatisfaction;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->idBesoin = $data['idBesoin'] ?? null;
        $this->dateSatisfaction = $data['dateSatisfaction'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM besoinSatisfait");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $satisfaits = [];
        foreach ($results as $row) {
            $satisfaits[] = new self($row);
        }
        return $satisfaits;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM besoinSatisfait WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new self($data) : null;
    }

    public static function getByBesoin($idBesoin) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM besoinSatisfait WHERE idBesoin = ?");
        $stmt->execute([$idBesoin]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $satisfaits = [];
        foreach ($results as $row) {
            $satisfaits[] = new self($row);
        }
        return $satisfaits;
    }

    public static function getByDateRange($dateDebut, $dateFin) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM besoinSatisfait WHERE dateSatisfaction BETWEEN ? AND ?");
        $stmt->execute([$dateDebut, $dateFin]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $satisfaits = [];
        foreach ($results as $row) {
            $satisfaits[] = new self($row);
        }
        return $satisfaits;
    }

    public function getBesoin() {
        if ($this->idBesoin) {
            return BesoinModel::getById($this->idBesoin);
        }
        return null;
    }

    public function save() {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->create();
        }
    }

    private function create() {
        $db = flight::db();
        $stmt = $db->prepare("INSERT INTO besoinSatisfait (idBesoin, dateSatisfaction) VALUES (?, ?)");
        $stmt->execute([$this->idBesoin, $this->dateSatisfaction]);
        $this->id = $db->lastInsertId();
        return $this;
    }

    private function update() {
        $db = flight::db();
        $stmt = $db->prepare("UPDATE besoinSatisfait SET idBesoin = ?, dateSatisfaction = ? WHERE id = ?");
        $stmt->execute([$this->idBesoin, $this->dateSatisfaction, $this->id]);
        return $this;
    }

    public function delete() {
        if ($this->id) {
            $db = flight::db();
            $stmt = $db->prepare("DELETE FROM besoinSatisfait WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }

    /**
     * Retourne les besoins restants (quantite_demande, quantite_attribue, quantite_restante)
     */
    public static function getBesoinRestant() {
        $db = flight::db();

        $sql = "SELECT b.id, b.idVille, v.nom AS ville, b.idProduit, p.description AS produit, p.pu AS pu, b.quantite AS quantite_demande, COALESCE(SUM(a.quantite),0) AS quantite_attribue, (b.quantite - COALESCE(SUM(a.quantite),0)) AS quantite_restante\n            FROM besoin b\n            LEFT JOIN attribution a ON a.idBesoin = b.id\n            JOIN produit p ON p.id = b.idProduit\n            JOIN ville v ON v.id = b.idVille\n            GROUP BY b.id\n            HAVING quantite_restante > 0";

        $stmt = $db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }
}
