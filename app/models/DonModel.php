<?php

namespace app\models;

use flight;

class DonModel {

    public $id;
    public $idProduit;
    public $quantite;
    public $dateDon;
    public $dateSaisie;
    public $quantiteRestante; // Propriété calculée pour affichage

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->idProduit = $data['idProduit'] ?? null;
        $this->quantite = $data['quantite'] ?? null;
        $this->dateDon = $data['dateDon'] ?? null;
        $this->dateSaisie = $data['dateSaisie'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM don");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $dons = [];
        foreach ($results as $row) {
            $dons[] = new self($row);
        }
        return $dons;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM don WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new self($data) : null;
    }

    public static function getByProduit($idProduit) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM don WHERE idProduit = ?");
        $stmt->execute([$idProduit]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $dons = [];
        foreach ($results as $row) {
            $dons[] = new self($row);
        }
        return $dons;
    }

    public static function getByDateRange($dateDebut, $dateFin) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM don WHERE dateDon BETWEEN ? AND ?");
        $stmt->execute([$dateDebut, $dateFin]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $dons = [];
        foreach ($results as $row) {
            $dons[] = new self($row);
        }
        return $dons;
    }

    public function getProduit() {
        if ($this->idProduit) {
            return ProduitModel::getById($this->idProduit);
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
        $stmt = $db->prepare("INSERT INTO don (idProduit, quantite, dateDon, dateSaisie) VALUES (?, ?, ?, now())");
        $stmt->execute([$this->idProduit, $this->quantite, $this->dateDon]);
        $this->id = $db->lastInsertId();
        return $this;
    }

    private function update() {
        $db = flight::db();
        $stmt = $db->prepare("UPDATE don SET idProduit = ?, quantite = ?, dateDon = ?, dateSaisie = ? WHERE id = ?");
        $stmt->execute([$this->idProduit, $this->quantite, $this->dateDon, $this->dateSaisie, $this->id]);
        return $this;
    }

    public function delete() {
        if ($this->id) {
            $db = flight::db();
            $stmt = $db->prepare("DELETE FROM don WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }

    public function getTotalArgent(){
        $db = flight::db();
        $stmt = $db->prepare("SELECT sum(quantite) as total FROM don where idProduit = ?");
        $stmt->execute([4]);
        $solde = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $solde['total'] ?? 0;
    }

    public static function getTotalArgentDisponible(){
        $db = flight::db();
        $stmt = $db->prepare("
            SELECT SUM(d.quantite - COALESCE(a.total_attribue, 0)) as argent_disponible
            FROM don d
            LEFT JOIN (
                SELECT idDon, SUM(quantite) as total_attribue
                FROM attribution
                GROUP BY idDon
            ) a ON d.id = a.idDon
            WHERE d.idProduit = 4
        ");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['argent_disponible'] ?? 0;
    }

    public static function donsDisponiblesPourProduit($idProduit): bool
{
    $db = flight::db();

    $stmt = $db->prepare("
        SELECT d.id, d.quantite, COALESCE(SUM(a.quantite), 0) as distribue
        FROM don d
        LEFT JOIN attribution a ON d.id = a.idDon
        WHERE d.idProduit = ?
        GROUP BY d.id, d.quantite
        HAVING d.quantite - distribue > 0
    ");

    $stmt->execute([$idProduit]);

    return $stmt->rowCount() > 0;
}

}
