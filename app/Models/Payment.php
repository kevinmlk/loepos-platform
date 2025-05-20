<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;

    const METHOD_TRANSFER = 'transfer';
    const METHOD_AUTOMATIC = 'automatic';
    const METHOD_CASH = 'cash';

    public const METHODS = [
        self::METHOD_TRANSFER,
        self::METHOD_AUTOMATIC,
        self::METHOD_CASH,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'document_id',
        'debt_id',
        'amount',
        'method',
    ];

    public function document() {
        return $this->belongsTo(Document::class);
    }

    public function debt() {
        return $this->belongsTo(Debt::class);
    }
}
