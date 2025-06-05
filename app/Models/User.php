<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // ENUM values
    const ROLE_EMPLOYEE = 'employee';
    const ROLE_ADMIN = 'admin';
    const ROLE_SUPERADMIN = 'superadmin';

    public const ROLES = [
        self::ROLE_EMPLOYEE,
        self::ROLE_ADMIN,
        self::ROLE_SUPERADMIN,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'organization_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relations
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function dossiers() {
        return $this->hasMany(Dossier::class);
    }

    public function uploads() {
        return $this->hasMany(Upload::class);
    }
}
