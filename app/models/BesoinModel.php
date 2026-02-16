<?php

namespace app\models;

use flight;

class BesoinModel {

    public $id;
    public $idType;
    public $idVille;
    public $idProduit;
    public $quantite;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->idType = $data['idType'] ?? null;
        $this->idVille = $data['idVille'] ?? null;
        $this->idProduit = $data['idProduit'] ?? null;
        $this->quantite = $data['quantite'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM besoin");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $besoins = [];
        foreach ($results as $row) {
            $besoins[] = new self($row);
        }
        return $besoins;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM besoin WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new self($data) : null;
    }

    public static function getByType($idType) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM besoin WHERE idType = ?");
        $stmt->execute([$idType]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $besoins = [];
        foreach ($results as $row) {
            $besoins[] = new self($row);
        }
        return $besoins;
    }

    public static function getByVille($idVille) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM besoin WHERE idVille = ?");
        $stmt->execute([$idVille]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $besoins = [];
        foreach ($results as $row) {
            $besoins[] = new self($row);
        }
        return $besoins;
    }

    public static function getByProduit($idProduit) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM besoin WHERE idProduit = ?");
        $stmt->execute([$idProduit]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $besoins = [];
        foreach ($results as $row) {
            $besoins[] = new self($row);
        }
        return $besoins;
    }

    public function getType() {
        if ($this->idType) {
            return TypeBesoinModel::getById($this->idType);
        }
        return null;
    }

    public function getVille() {
        if ($this->idVille) {
            return VilleModel::getById($this->idVille);
        }
        return null;
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
        $stmt = $db->prepare("INSERT INTO besoin (idType, idVille, idProduit, quantite) VALUES (?, ?, ?, ?)");
        $stmt->execute([$this->idType, $this->idVille, $this->idProduit, $this->quantite]);
        $this->id = $db->lastInsertId();
        return $this;
    }

    private function update() {
        $db = flight::db();
        $stmt = $db->prepare("UPDATE besoin SET idType = ?, idVille = ?, idProduit = ?, quantite = ? WHERE id = ?");
        $stmt->execute([$this->idType, $this->idVille, $this->idProduit, $this->quantite, $this->id]);
        return $this;
    }

    public function delete() {
        if ($this->id) {
            $db = flight::db();
            $stmt = $db->prepare("DELETE FROM besoin WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }
}
