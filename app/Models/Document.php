<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'mime_type',
        'parsed_data',
    ];

    //
}
