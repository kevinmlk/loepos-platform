<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    // ENUM Values
    const TYPE_INVOICE = 'invoice';
    const TYPE_REMINDER = 'reminder';
    const TYPE_IDENTITY = 'identity';
    const TYPE_AGREEMENT = 'agreement';

    public const TYPES = [
        self::TYPE_INVOICE,
        self::TYPE_REMINDER,
        self::TYPE_IDENTITY,
        self::TYPE_AGREEMENT,
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';

    public const STATUS = [
        self::STATUS_PENDING,
        self::STATUS_VERIFIED,
        self::STATUS_REJECTED,
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
    public function upload() {
        return $this->belongsTo(Upload::class);
    }

    public function dossier() {
        return $this->belongsTo(Dossier::class);
    }

    public function pages() {
        return $this->hasMany(Page::class);
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }

    public function tasks() {
        return $this->hasMany(Task::class);
    }
}
