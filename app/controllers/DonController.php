<?php
namespace app\controllers;

use flight\Engine;
use app\models\DonModel;

class DonController {
    protected Engine $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function index() {
        return DonModel::getAll();
    }

    public function get($id) {
        return DonModel::getById($id);
    }

    public function create($data) {
        $d = new DonModel($data);
        return $d->save();
    }

    public function delete($id) {
        $d = DonModel::getById($id);
        if ($d) return $d->delete();
        return false;
    }
}
