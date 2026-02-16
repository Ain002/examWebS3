<?php

namespace app\models;

use flight;

class TypeBesoinModel {

    public $id;
    public $description;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->description = $data['description'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM typeBesoin");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $types = [];
        foreach ($results as $row) {
            $types[] = new self($row);
        }
        return $types;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM typeBesoin WHERE id = ?");
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
        $stmt = $db->prepare("INSERT INTO typeBesoin (description) VALUES (?)");
        $stmt->execute([$this->description]);
        $this->id = $db->lastInsertId();
        return $this;
    }

    private function update() {
        $db = flight::db();
        $stmt = $db->prepare("UPDATE typeBesoin SET description = ? WHERE id = ?");
        $stmt->execute([$this->description, $this->id]);
        return $this;
    }

    public function delete() {
        if ($this->id) {
            $db = flight::db();
            $stmt = $db->prepare("DELETE FROM typeBesoin WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }
}
