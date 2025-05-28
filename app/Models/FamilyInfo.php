<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyInfo extends Model
{
    /** @use HasFactory<\Database\Factories\FamilyInfoFactory> */
    use HasFactory;

    const STATUS_SINGLE = 'single';
    const STATUS_MARRIED = 'married';
    const STATUS_LIVING_TOGETHER = 'living together';
    const STATUS_DIVORCED = 'divorced';
    const STATUS_WIDOWED = 'widowed';

    public const STATUS = [
        self::STATUS_SINGLE,
        self::STATUS_MARRIED,
        self::STATUS_LIVING_TOGETHER,
        self::STATUS_DIVORCED,
        self::STATUS_WIDOWED,
    ];

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
