<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDocumentImage extends Model
{   
    protected $table = 'drivers_document_image';

    protected $fillable = [
        'drivers_document_id',
        'name',
    ];
}
