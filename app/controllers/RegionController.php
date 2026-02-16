<?php
namespace app\controllers;

use flight\Engine;
use app\models\RegionModel;

class RegionController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function index() {
        return RegionModel::getAll();
    }

    public function get($id) {
        return RegionModel::getById($id);
    }

    public function create($data) {
        $r = new RegionModel($data);
        return $r->save();
    }

    public function delete($id) {
        $r = RegionModel::getById($id);
        if ($r) return $r->delete();
        return false;
    }
}
