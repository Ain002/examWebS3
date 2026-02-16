<?php

namespace app\models;

use flight;

class DonDistribueModel {

    public $id;
    public $idDon;
    public $dateDistribution;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    public function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->idDon = $data['idDon'] ?? null;
        $this->dateDistribution = $data['dateDistribution'] ?? null;
    }

    public static function getAll() {
        $db = flight::db();
        $stmt = $db->query("SELECT * FROM donDistribue");
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $distribues = [];
        foreach ($results as $row) {
            $distribues[] = new self($row);
        }
        return $distribues;
    }

    public static function getById($id) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM donDistribue WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $data ? new self($data) : null;
    }

    public static function getByDon($idDon) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM donDistribue WHERE idDon = ?");
        $stmt->execute([$idDon]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $distribues = [];
        foreach ($results as $row) {
            $distribues[] = new self($row);
        }
        return $distribues;
    }

    public static function getByDateRange($dateDebut, $dateFin) {
        $db = flight::db();
        $stmt = $db->prepare("SELECT * FROM donDistribue WHERE dateDistribution BETWEEN ? AND ?");
        $stmt->execute([$dateDebut, $dateFin]);
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $distribues = [];
        foreach ($results as $row) {
            $distribues[] = new self($row);
        }
        return $distribues;
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
        $stmt = $db->prepare("INSERT INTO donDistribue (idDon, dateDistribution) VALUES (?, ?)");
        $stmt->execute([$this->idDon, $this->dateDistribution]);
        $this->id = $db->lastInsertId();
        return $this;
    }

    private function update() {
        $db = flight::db();
        $stmt = $db->prepare("UPDATE donDistribue SET idDon = ?, dateDistribution = ? WHERE id = ?");
        $stmt->execute([$this->idDon, $this->dateDistribution, $this->id]);
        return $this;
    }

    public function delete() {
        if ($this->id) {
            $db = flight::db();
            $stmt = $db->prepare("DELETE FROM donDistribue WHERE id = ?");
            return $stmt->execute([$this->id]);
        }
        return false;
    }
}
