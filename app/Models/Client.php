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
    public function dossiers() {
        return $this->hasMany(Dossier::class);
    }

    public function financialInfo() {
        return $this->hasOne(FinancialInfo::class);
    }

    public function familyInfo() {
        return $this->hasOne(FamilyInfo::class);
    }

    
    // Accessor for full address
    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->postal_code} {$this->city}, {$this->country}";
    }
}
