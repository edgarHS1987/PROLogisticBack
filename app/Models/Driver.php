<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [
        'names',
        'users_id',
        'lastname1',
        'lastname2',
        'photo',
        'rfc',
        'status'
    ];
}
