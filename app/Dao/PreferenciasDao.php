<?php

namespace App\Dao;

use App\Dao\BaseDao;
use App\Models\Preferencias;

class PreferenciasDao extends BaseDao {
    
    public function getModel(): string {
        return Preferencias::class;
    }

    public function findByUsuarioId(string $usuarioId) {
        $preferenciasWhere = $this->model::where('usuarioId', $usuarioId)->take(1)->get();

        if (sizeof($preferenciasWhere) > 0) {
            $preferencias = $preferenciasWhere[0];
            $this->transform($preferencias);

            return $preferencias;
        }

        return null;
    }
}
