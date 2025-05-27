<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    // Enum values
    const TYPE_EXTRACTION = 'extraction';
    const TYPE_CLASSIFICATION = 'classification';
    const TYPE_VALIDATION = 'validation';

    public const TYPES = [
        self::TYPE_EXTRACTION,
        self::TYPE_CLASSIFICATION,
        self::TYPE_VALIDATION
    ];

    // Relations
    public function document() {
        return $this->belongsTo(Document::class);
    }
}
