<?php

namespace App\Http\Controllers;

use App\Dao\TecnologiaDao;
use App\Http\Controllers\Controller;

class TecnologiaRestController extends Controller {
    private TecnologiaDao $tecnologiaDao;

    public function __construct() {
        $this->tecnologiaDao = new TecnologiaDao;
    }

    public function findAll() {
        $tecnologiaList = $this->tecnologiaDao->findAll();

        return response()->json($tecnologiaList, 200);
    }    
}
