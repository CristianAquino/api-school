<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'first_name',
        'second_name',
        'phone',
        'birth_date',
        'address',
        'dni'
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

    // Mutator para encriptar automáticamente la contraseña
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

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

    // definiendo roles
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';
    const ROLE_TEACHER = 'ROLE_TEACHER';
    const ROLE_STUDENT = 'ROLE_STUDENT';
    private const ROLES_HIERARCHY = [
        self::ROLE_SUPERADMIN => [self::ROLE_TEACHER],
        self::ROLE_TEACHER => [self::ROLE_STUDENT],
        self::ROLE_STUDENT => [],
    ];

    private static function isRoleHierarchy($role, $role_hierarchy)
    {
        if (in_array($role, $role_hierarchy)) {
            return true;
        }
        foreach ($role_hierarchy as $role_included) {
            if (self::isRoleHierarchy($role, self::ROLES_HIERARCHY[$role_included])) {
                return true;
            }
        }
        return false;
    }

    public function isGranted($role): bool
    {
        if ($role == $this->role) {
            return true;
        }
        return self::isRoleHierarchy($role, self::ROLES_HIERARCHY[$this->role]);
    }

    // implementacion de metodos para JWTAuth
    public function getJWTIdentifier()
    {
        return $this->getKey(); // ID del usuario
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function userable(): MorphTo
    {
        return $this->morphTo();
    }
}
