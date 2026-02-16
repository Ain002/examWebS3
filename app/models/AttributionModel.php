<?php

namespace app\models;

use flight;

class AttributionModel {

    public $id;
    public $idBesoin;
    public $idDon;
    public $quantite;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->idBesoin = $data['idBesoin'] ?? null;
        $this->idDon = $data['idDon'] ?? null;
        $this->quantite = $data['quantite'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM attribution");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $attributions = [];
        foreach ($results as $row) {
            $attributions[] = new self($row);
        }
        return $attributions;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM attribution WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new self($data) : null;
    }

    public static function getByBesoin($idBesoin) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM attribution WHERE idBesoin = ?");
        $stmt->execute([$idBesoin]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $attributions = [];
        foreach ($results as $row) {
            $attributions[] = new self($row);
        }
        return $attributions;
    }

    public static function getByDon($idDon) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM attribution WHERE idDon = ?");
        $stmt->execute([$idDon]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $attributions = [];
        foreach ($results as $row) {
            $attributions[] = new self($row);
        }
        return $attributions;
    }

    public function getBesoin() {
        if ($this->idBesoin) {
            return BesoinModel::getById($this->idBesoin);
        }
        return null;
    }

    public function getDon() {
        if ($this->idDon) {
            return DonModel::getById($this->idDon);
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
        $stmt = $db->prepare("INSERT INTO attribution (idBesoin, idDon, quantite) VALUES (?, ?, ?)");
        $stmt->execute([$this->idBesoin, $this->idDon, $this->quantite]);
        $this->id = $db->lastInsertId();
        return $this;
    }

    private function update() {
        $db = flight::db();
        $stmt = $db->prepare("UPDATE attribution SET idBesoin = ?, idDon = ?, quantite = ? WHERE id = ?");
        $stmt->execute([$this->idBesoin, $this->idDon, $this->quantite, $this->id]);
        return $this;
    }

    public function delete() {
        if ($this->id) {
            $db = flight::db();
            $stmt = $db->prepare("DELETE FROM attribution WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }
    
}
