<?php

namespace app\models;

use flight;

class ProduitModel {

    public $id;
    public $description;
    public $pu;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->pu = $data['pu'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM produit");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $produits = [];
        foreach ($results as $row) {
            $produits[] = new self($row);
        }
        return $produits;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM produit WHERE id = ?");
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
        $stmt = $db->prepare("INSERT INTO produit (description, pu) VALUES (?, ?)");
        $stmt->execute([$this->description, $this->pu]);
        $this->id = $db->lastInsertId();
        return $this;
    }

    private function update() {
        $db = flight::db();
        $stmt = $db->prepare("UPDATE produit SET description = ?, pu = ? WHERE id = ?");
        $stmt->execute([$this->description, $this->pu, $this->id]);
        return $this;
    }

    public function delete() {
        if ($this->id) {
            $db = flight::db();
            $stmt = $db->prepare("DELETE FROM produit WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }
}
