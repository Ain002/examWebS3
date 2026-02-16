<?php

namespace app\models;

use flight;

class VilleModel {

    public $id;
    public $idRegion;
    public $nom;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }
    

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->idRegion = $data['idRegion'] ?? null;
        $this->nom = $data['nom'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM ville");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $villes = [];
        foreach ($results as $row) {
            $villes[] = new self($row);
        }
        return $villes;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM ville WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new self($data) : null;
    }

    public static function getByRegion($idRegion) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM ville WHERE idRegion = ?");
        $stmt->execute([$idRegion]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $villes = [];
        foreach ($results as $row) {
            $villes[] = new self($row);
        }
        return $villes;
    }

    public function getRegion() {
        if ($this->idRegion) {
            return RegionModel::getById($this->idRegion);
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
        $stmt = $db->prepare("INSERT INTO ville (idRegion, nom) VALUES (?, ?)");
        $stmt->execute([$this->idRegion, $this->nom]);
        $this->id = $db->lastInsertId();
        return $this;
    }

    private function update() {
        $db = flight::db();
        $stmt = $db->prepare("UPDATE ville SET idRegion = ?, nom = ? WHERE id = ?");
        $stmt->execute([$this->idRegion, $this->nom, $this->id]);
        return $this;
    }

    public function delete() {
        if ($this->id) {
            $db = flight::db();
            $stmt = $db->prepare("DELETE FROM ville WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }
}
