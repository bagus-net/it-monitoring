<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    public const ROLE_MASTER = 'master';
    public const ROLE_ADMIN_IT = 'admin_it';
    public const ROLE_USER = 'user';

    public const ROLE_LABELS = [
        self::ROLE_MASTER => 'Master',
        self::ROLE_ADMIN_IT => 'Admin IT',
        self::ROLE_USER => 'User / Karyawan',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'is_active',
        'signature_path',
        'signature_title',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function isMaster(): bool
    {
        return $this->role === self::ROLE_MASTER;
    }

    public function isAdminIt(): bool
    {
        return $this->role === self::ROLE_ADMIN_IT;
    }

    public function isEmployee(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? $this->role;
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function accessibleIsoDocuments()
    {
        return $this->belongsToMany(IsoDocument::class, 'iso_document_user')->withTimestamps();
    }
}
