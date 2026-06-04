<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'EmployeeID',
        'name',
        'email',
        'Photo',
        'password',
        'Status',
        'LastLogin',
        'Role',
        'role_id',
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
    ];

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if (! Schema::hasTable('roles') || ! Schema::hasColumn($user->getTable(), 'role_id')) {
                return;
            }

            if ($user->role_id) {
                $role = Role::query()->find($user->role_id);

                if ($role) {
                    $user->Role = $role->name;

                    return;
                }
            }

            if ($user->Role) {
                $role = Role::query()->firstOrCreate(
                    ['name' => $user->Role],
                    [
                        'slug' => Str::slug($user->Role),
                        'is_system' => false,
                    ]
                );

                $user->role_id = $role->id;
                $user->Role = $role->name;
            }
        });
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole(string|array $roles): bool
    {
        $roleNames = (array) $roles;

        if (! $this->relationLoaded('role')) {
            $this->loadMissing('role.permissions');
        }

        return in_array($this->role?->name, $roleNames, true);
    }

    public function hasPermission(string $permissionName): bool
    {
        if (! $this->relationLoaded('role')) {
            $this->loadMissing('role.permissions');
        }

        return $this->role?->permissions->contains('name', $permissionName) ?? false;
    }

    public function getDisplayNameAttribute(): string
    {
        return match ($this->name) {
            'Hotelio Admin', 'Hotello Admin' => 'Hot-L Admin',
            default => $this->name,
        };
    }
}
