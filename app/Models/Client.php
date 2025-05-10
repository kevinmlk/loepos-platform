<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    // Relations
    public function dossier() {
        return $this->hasMany(Dossier::class);
    }

    public function financialInfo() {
        return $this->hasOne(FinancialInfo::class);
    }
}
