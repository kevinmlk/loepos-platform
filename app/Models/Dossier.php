<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Dossier extends Model
{
    /** @use HasFactory<\Database\Factories\DossierFactory> */
    use HasFactory;

    // ENUM Values
    const STATUS_ACTIVE = 'active';
    const STATUS_IN_PROCESS = 'in process';
    const STATUS_CLOSED = 'closed';

    public const STATUS = [
        self::STATUS_ACTIVE,
        self::STATUS_IN_PROCESS,
        self::STATUS_CLOSED,
    ];

    const TYPE_DEBT_MEDIATION = 'schuldbemiddeling';
    const TYPE_BUDGET_MANAGEMENT = 'budgetbeheer';

    public const TYPES = [
        self::TYPE_DEBT_MEDIATION,
        self::TYPE_BUDGET_MANAGEMENT,
    ];

    protected $fillable = [
        'client_id',
        'user_id',
        'status',
        'type',
    ];

    // Relations
    public function client() {
        return $this->belongsTo(Client::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function documents() {
        return $this->hasMany(Document::class);
    }

    public function debts() {
        return $this->hasMany(Debt::class);
    }
}
