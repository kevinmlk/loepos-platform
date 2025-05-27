<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifiedDocument extends Model
{
    use HasFactory;

    // Document types
    const TYPE_INVOICE = 'invoice';
    const TYPE_REMINDER = 'reminder';
    const TYPE_IDENTITY = 'identity';
    const TYPE_AGREEMENT = 'agreement';
    const TYPE_LETTER = 'letter';
    const TYPE_CONTRACT = 'contract';
    const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_INVOICE,
        self::TYPE_REMINDER,
        self::TYPE_IDENTITY,
        self::TYPE_AGREEMENT,
        self::TYPE_LETTER,
        self::TYPE_CONTRACT,
        self::TYPE_OTHER,
    ];

    protected $fillable = [
        'organization_id',
        'client_id',
        'dossier_id',
        'original_document_id',
        'type',
        'file_name',
        'file_path',
        'sender',
        'receiver',
        'send_date',
        'receive_date',
        'due_date',
        'verified_data',
        'metadata'
    ];

    protected $casts = [
        'send_date' => 'date',
        'receive_date' => 'date',
        'due_date' => 'date',
        'verified_data' => 'array',
        'metadata' => 'array'
    ];

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function originalDocument()
    {
        return $this->belongsTo(Document::class, 'original_document_id');
    }

    // Scopes
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeForDossier($query, $dossierId)
    {
        return $query->where('dossier_id', $dossierId);
    }
}