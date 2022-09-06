<?php

namespace App\Dao;

use App\Dao\BaseDao;
use App\Models\User;

class UserDao extends BaseDao {
    
    public function getModel(): string {
        return User::class;
    }
}
