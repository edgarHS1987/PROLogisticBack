<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverVehicle extends Model
{   
    protected $table = 'drivers_vehicle';

    protected $fillable = [
        'drivers_id',   
        'brand',
        'model',
        'color',
        'plate',
        'year',
    ];
}
