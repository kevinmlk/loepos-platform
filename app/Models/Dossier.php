<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Dossier extends Model
{
    /** @use HasFactory<\Database\Factories\DossierFactory> */
    use HasFactory;

    // ENUM Values
    const STATUS_ACTIVE = 'actief';
    const STATUS_IN_PROGRESS = 'in uitvoering';
    const STATUS_CLOSED = 'afgesloten';

    public const STATUS = [
        self::STATUS_ACTIVE,
        self::STATUS_IN_PROGRESS,
        self::STATUS_CLOSED,
    ];

    // Relations
    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
