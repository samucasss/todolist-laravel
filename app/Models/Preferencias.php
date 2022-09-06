<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Preferencias extends Model {
    
    protected $connection = 'mongodb';
    protected $collection = 'preferencias';
    
    protected $fillable = [
        'tipoFiltro', 'done', 'usuarioId'
    ];  
    
    protected $hidden = ['created_at', 'updated_at'];
}
