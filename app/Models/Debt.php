<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    /** @use HasFactory<\Database\Factories\DebtFactory> */
    use HasFactory;

    // ENUM values
    const STATUS_OPEN = 'open';
    const STATUS_SETTLED = 'settled';
    const STATUS_SUSPENDED = 'suspended';

    public const STATUS = [
        self::STATUS_OPEN,
        self::STATUS_SETTLED,
        self::STATUS_SUSPENDED,
    ];

    // Relations
    public function dossier() {
        return $this->belongsTo(Dossier::class);
    }
}
