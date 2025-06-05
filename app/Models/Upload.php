<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_VERIFIED = 'verified';
    const STATUS_REJECTED = 'rejected';

    public const STATUS = [
        self::STATUS_PENDING,
        self::STATUS_VERIFIED,
        self::STATUS_REJECTED,
    ];

    protected $fillable = [
        'file_name',
        'file_path',
        'parsed_data',
        'documents',
        'status',
    ];

    public function documents() {
        return $this->hasMany(Document::class);
    }
}
