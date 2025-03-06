<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Region extends Model
{
    protected $fillable = [
        'name', 'soiltype', 'manager_id', 'coordinates', 'color', 'info'
    ];
    

}
