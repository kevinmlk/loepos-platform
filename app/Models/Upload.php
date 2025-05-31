<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    const STATUS_FAILED = 'failed';
    const STATUS_IN_PROCESS = 'in process';
    const STATUS_UPLOADED = 'uploaded';

    public const STATUS = [
        self::STATUS_FAILED,
        self::STATUS_IN_PROCESS,
        self::STATUS_UPLOADED,
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
