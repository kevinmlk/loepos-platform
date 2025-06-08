<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialInfo extends Model
{
    /** @use HasFactory<\Database\Factories\FinancialInfoFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'client_id',
        'iban',
        'bank_name',
        'monthly_income',
        'monthly_expenses',
        'employer',
        'contract',
        'education'
    ];

    // Enum values - CONTRACT
    const CONTRACT_PERMANENT = 'permanent';
    const CONTRACT_TEMPORARY = 'temporary';
    const CONTRACT_SELF_EMPLOYED = 'self-employed';
    const CONTRACT_UNEMPLOYED = 'unemployed';

    public const CONTRACTS = [
        self::CONTRACT_PERMANENT,
        self::CONTRACT_TEMPORARY,
        self::CONTRACT_SELF_EMPLOYED,
        self::CONTRACT_UNEMPLOYED
    ];

    // Enum values - EDUCATION
    const EDUCATION_PRIMARY = 'primary';
    const EDUCATION_SECONDARY = 'secondary';
    const EDUCATION_HIGHER = 'higher';

    public const EDUCATIONS = [
        self::EDUCATION_PRIMARY,
        self::EDUCATION_SECONDARY,
        self::EDUCATION_HIGHER
    ];

    // Relations
    public function client() {
        return $this->belongsTo(Client::class);
    }
}
