<?php

namespace app\models;

use flight;

class ConfigFraisAchatModel {

    public $id;
    public $pourcentage;
    public $dateCreation;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->pourcentage = $data['pourcentage'] ?? null;
        $this->dateCreation = $data['dateCreation'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM configFraisAchat ORDER BY dateCreation DESC");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $configs = [];
        foreach ($results as $row) {
            $configs[] = new ConfigFraisAchatModel($row);
        }
        return $configs;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM configFraisAchat WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new ConfigFraisAchatModel($data) : null;
    }

    public static function getLatest() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM configFraisAchat ORDER BY dateCreation DESC LIMIT 1");
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new ConfigFraisAchatModel($data) : null;
    }

    public function save() {
        $db = flight::db();
        
        if ($this->id) {
            $stmt = $db->prepare("UPDATE configFraisAchat SET pourcentage = ?, dateCreation = ? WHERE id = ?");
            $stmt->execute([$this->pourcentage, $this->dateCreation, $this->id]);
            return $this->id;
        } else {
            $stmt = $db->prepare("INSERT INTO configFraisAchat (pourcentage, dateCreation) VALUES (?, ?)");
            $stmt->execute([$this->pourcentage, $this->dateCreation]);
            $this->id = $db->lastInsertId();
            return $this->id;
        }
    }

    public function delete() {
        if (!$this->id) return false;
        
        $db = flight::db();
        $stmt = $db->prepare("DELETE FROM configFraisAchat WHERE id = ?");
        return $stmt->execute([$this->id]);
    }
}
