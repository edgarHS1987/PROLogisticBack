<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverAddress extends Model
{   
    protected $table = 'drivers_address';

    protected $fillable = [
        'drivers_id',
        'street',
        'int_number',
        'ext_number',
        'colony',
        'state',
        'municipality',
        'zip_code',
        'isFiscal',
    ];
}
