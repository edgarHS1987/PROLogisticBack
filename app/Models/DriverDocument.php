<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{   
    protected $table = 'drivers_document';

    protected $fillable = [
        'drivers_id',
        'type',
        'expiration_date',
        'number',
    ];
}
