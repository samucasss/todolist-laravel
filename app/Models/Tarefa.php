<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Tarefa extends Model {
    
    protected $connection = 'mongodb';
    protected $collection = 'tarefas';
    
    protected $fillable = [
        'data', 'nome', 'descricao', 'done', 'usuarioId'
    ];    

    protected $hidden = ['created_at', 'updated_at'];

}
