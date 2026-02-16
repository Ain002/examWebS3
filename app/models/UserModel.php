<?php

namespace app\models;

use flight;

class UserModel {

    public function getAllUsers() {
       $users = [
			[ 'id' => 1, 'name' => 'Bob Jones', 'email' => 'bob@example.com' ],
			[ 'id' => 2, 'name' => 'Bob Smith', 'email' => 'bsmith@example.com' ],
			[ 'id' => 3, 'name' => 'Nia Itokiana', 'email' => 'nia@gmail.com' ],
		];
        return $users;
    }
    public function getAllUser($id) {
       $users = [
			[ 'id' => 1, 'name' => 'Bob Jones', 'email' => 'bob@example.com' ],
			[ 'id' => 2, 'name' => 'Bob Smith', 'email' => 'bsmith@example.com' ],
			[ 'id' => 3, 'name' => 'Nia Itokiana', 'email' => 'nia@gmail.com' ],
		];
        return $users[$id-1];
    }
}


