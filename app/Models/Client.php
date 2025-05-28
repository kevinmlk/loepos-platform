<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    /** @use HasFactory<\Database\Factories\ClientFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'postal_code',
        'country',
        'national_registry_number'
    ];

    // Relations
    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function dossiers() {
        return $this->belongsToMany(Dossier::class, 'client_dossier')->withTimestamps();
    }

    public function financialInfo() {
        return $this->hasOne(FinancialInfo::class);
    }
}
