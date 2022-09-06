<?php

namespace App\Dao;

use App\Dao\BaseDao;
use App\Models\Tecnologia;

class TecnologiaDao extends BaseDao {
    
    public function getModel(): string {
        return Tecnologia::class;
    }
}
