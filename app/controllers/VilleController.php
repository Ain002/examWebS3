<?php
namespace app\controllers;

use flight\Engine;
use app\models\VilleModel;

class VilleController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function index() {
        return VilleModel::getAll();
    }

    public function get($id) {
        return VilleModel::getById($id);
    }

    public function byRegion($idRegion) {
        return VilleModel::getByRegion($idRegion);
    }

    public function create($data) {
        $v = new VilleModel($data);
        return $v->save();
    }

    public function delete($id) {
        $v = VilleModel::getById($id);
        if ($v) return $v->delete();
        return false;
    }
}
