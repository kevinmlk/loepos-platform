<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    /** @use HasFactory<\Database\Factories\OrganizationFactory> */
    use HasFactory;

    // Relations
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Protected $table = 'organizations';
    protected $fillable = [
        'name', 'email', 'phone','status', 'website', 'VAT', 'address', 'postal_code', 'city', 'country',
    ];

    // Accessor for full address
    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->postal_code} {$this->city}, {$this->country}";
    }
}
