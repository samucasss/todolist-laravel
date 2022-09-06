<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Tecnologia extends Model {
    protected $connection = 'mongodb';
    protected $collection = 'tecnologias';
    
    protected $fillable = [
        'nome','tipo'
    ];    

    protected $hidden = ['created_at', 'updated_at'];
}
