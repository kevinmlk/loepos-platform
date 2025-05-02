<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'file_name',
        'file_path',
        'parsed_data',
    ];

    // Relations
    public function dossier() {
        return $this->belongsTo(Dossier::class);
    }
}
