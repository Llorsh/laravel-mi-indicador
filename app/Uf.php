<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Uf extends Model
{
    protected $fillable = [
        'fecha', 'valor'
    ];

    protected $hidden = [
        'id', 'created_at', 'updated_at'
    ];
}
