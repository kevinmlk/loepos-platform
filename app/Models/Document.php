<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    // ENUM Values
    const TYPE_BILL = 'factuur';
    const TYPE_REMINDER = 'herinnering';
    const TYPE_IDENTITY = 'identiteit';
    const TYPE_AGREEMENT = 'agreement';

    public const TYPES = [
        self::TYPE_BILL,
        self::TYPE_REMINDER,
        self::TYPE_IDENTITY,
        self::TYPE_AGREEMENT,
    ];

    protected $fillable = [
        'dossier_id',
        'type',
        'file_name',
        'file_path',
        'parsed_data',
        'verified_status'
    ];

    // Relations
    public function dossier() {
        return $this->belongsTo(Dossier::class);
    }
}
