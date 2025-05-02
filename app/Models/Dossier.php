<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Dossier extends Model
{
    /** @use HasFactory<\Database\Factories\DossierFactory> */
    use HasFactory;

    // Relations
    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
