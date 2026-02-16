<?php

namespace app\models;

use flight;

class RegionModel {

    public $id;
    public $nom;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->nom = $data['nom'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM region");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $regions = [];
        foreach ($results as $row) {
            $regions[] = new self($row);
        }
        return $regions;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM region WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new self($data) : null;
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
        $stmt = $db->prepare("INSERT INTO region (nom) VALUES (?)");
        $stmt->execute([$this->nom]);
        $this->id = $db->lastInsertId();
        return $this;
    }

    private function update() {
        $db = flight::db();
        $stmt = $db->prepare("UPDATE region SET nom = ? WHERE id = ?");
        $stmt->execute([$this->nom, $this->id]);
        return $this;
    }

    public function delete() {
        if ($this->id) {
            $db = flight::db();
            $stmt = $db->prepare("DELETE FROM region WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }
}
