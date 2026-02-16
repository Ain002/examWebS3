<?php

namespace app\models;

use flight;

class AchatModel {

    public $id;
    public $idBesoin;
    public $montant;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->idBesoin = $data['idBesoin'] ?? null;
        $this->montant = $data['montant'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM achat");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $achats = [];
        foreach ($results as $row) {
            $achats[] = new AchatModel($row);
        }
        return $achats;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM achat WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new AchatModel($data) : null;
    }

    public static function getByBesoin($idBesoin) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM achat WHERE idBesoin = ?");
        $stmt->execute([$idBesoin]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new AchatModel($data) : null;
    }

    public function save() {
        $db = flight::db();
        
        if ($this->id) {
            $stmt = $db->prepare("UPDATE achat SET idBesoin = ?, montant = ? WHERE id = ?");
            $stmt->execute([$this->idBesoin, $this->montant, $this->id]);
            return $this->id;
        } else {
            $stmt = $db->prepare("INSERT INTO achat (idBesoin, montant) VALUES (?, ?)");
            $stmt->execute([$this->idBesoin, $this->montant]);
            $this->id = $db->lastInsertId();
            return $this->id;
        }
    }

    public function delete() {
        if (!$this->id) return false;
        
        $db = flight::db();
        $stmt = $db->prepare("DELETE FROM achat WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

    public function getBesoin() {
        return BesoinModel::getById($this->idBesoin);
    }
}
