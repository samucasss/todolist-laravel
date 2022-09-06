<?php

namespace App\Dao;

use App\Dao\BaseDao;
use App\Models\Tarefa;
use Illuminate\Database\Eloquent\Collection;

class TarefaDao extends BaseDao {
    
    public function getModel(): string {
        return Tarefa::class;
    }

    public function findAllByDataBetweenAndUsuarioId($inicio, $fim, string $usuarioId): Collection {
        $tarefaList = $this->model::where('usuarioId', $usuarioId)->
            whereBetween('data', array($inicio, $fim))->get();

        if (sizeof($tarefaList) > 0) {
            foreach ($tarefaList as $tarefa) {
                $this->transform($tarefa);
            }            
        }

        return $tarefaList;
    }
}
