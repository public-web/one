<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $password_changed_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property bool $active
 * @property bool $require_2fa
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Permission\Models\Role[] $roles
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_changed_at',
        'active',
        'expires_at',
        'require_2fa',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'password_changed_at' => 'datetime',
            'expires_at' => 'datetime',
            'password' => 'hashed',
            'active' => 'boolean',
            'require_2fa' => 'boolean',
        ];
    }

    /**
     * Check if the user needs to change their password on first login
     */
    public function needsPasswordChange(): bool
    {
        return is_null($this->password_changed_at);
    }

    /**
     * Check if the user account has expired
     */
    public function hasExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Check if the user account is active and not expired
     */
    public function isActiveAndNotExpired(): bool
    {
        return $this->active && ! $this->hasExpired();
    }

    /**
     * Check if the user is a superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Check if the user is an admin (includes superadmin)
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('superadmin');
    }

    /**
     * Check if the user can manage users
     */
    public function canManageUsers(): bool
    {
        return $this->can('users.list') && $this->can('users.edit');
    }

    /**
     * Get the user's primary role name
     *
     * Note: Uses eager-loaded roles if available to avoid N+1 queries.
     * When using in loops, ensure roles are eager-loaded: User::with('roles')->get()
     */
    public function getPrimaryRole(): ?string
    {
        // Use relationLoaded to optimize: if not loaded, load only what we need
        if (!$this->relationLoaded('roles')) {
            return $this->roles()->value('name');
        }

        /** @var \Spatie\Permission\Models\Role|null $role */
        $role = $this->roles->first();

        return $role?->name;
    }

    /**
     * Enable two-factor authentication for the user
     */
    public function enableTwoFactor(): void
    {
        $secret = app(\Laravel\Fortify\TwoFactorAuthenticationProvider::class)->generateSecretKey();

        $this->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode(
                collect(range(1, 8))->map(function () {
                    return \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10);
                })->toArray()
            )),
            'two_factor_confirmed_at' => now(),
        ])->save();
    }

    /**
     * Disable two-factor authentication for the user
     */
    public function disableTwoFactor(): void
    {
        $this->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();
    }

    /**
     * Scope to filter only active users
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to filter only non-expired users
     */
    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to filter active and non-expired users
     */
    public function scopeActiveAndNotExpired($query)
    {
        return $query->active()->notExpired();
    }

    /**
     * Scope to filter users by role
     */
    public function scopeWithRole($query, string $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Scope to search users by name or email
     */
    public function scopeSearch($query, ?string $search)
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter users by role (alias for withRole for consistency)
     */
    public function scopeByRole($query, ?string $role)
    {
        if (empty($role)) {
            return $query;
        }

        return $query->withRole($role);
    }

    /**
     * Scope to filter users by status (active, inactive, deleted)
     */
    public function scopeByStatus($query, ?string $status)
    {
        return match($status) {
            'active' => $query->where('active', true)->whereNull('deleted_at'),
            'inactive' => $query->where('active', false)->whereNull('deleted_at'),
            'deleted' => $query->onlyTrashed(),
            default => $query
        };
    }

    /**
     * Scope to filter users by expiration status
     */
    public function scopeExpiring($query, ?string $expiring)
    {
        return match($expiring) {
            'soon' => $query->whereBetween('expires_at', [now(), now()->addDays(30)]),
            'expired' => $query->where('expires_at', '<', now()),
            default => $query
        };
    }

    /**
     * Get import/export operations initiated by this user
     */
    public function importExports()
    {
        return $this->hasMany(\App\Models\UserImportExport::class);
    }

    /**
     * Get only import operations
     */
    public function imports()
    {
        return $this->importExports()->where('type', \App\Enums\ImportExportType::Import);
    }

    /**
     * Get only export operations
     */
    public function exports()
    {
        return $this->importExports()->where('type', \App\Enums\ImportExportType::Export);
    }

    /**
     * Get recent import/export operations (last 30 days)
     */
    public function recentImportExports(int $days = 30)
    {
        return $this->importExports()
            ->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get import/export statistics for this user
     *
     * Uses SQL COUNT() queries for efficiency - does not load records into memory.
     * Much more efficient than loading all records when user has many operations.
     *
     * @return array{total: int, imports: int, exports: int, completed: int, failed: int, pending: int, processing: int}
     */
    public function getImportExportStats(): array
    {
        return [
            'total' => $this->importExports()->count(),
            'imports' => $this->imports()->count(),
            'exports' => $this->exports()->count(),
            'completed' => $this->importExports()->where('status', \App\Enums\ImportExportStatus::Completed)->count(),
            'failed' => $this->importExports()->where('status', \App\Enums\ImportExportStatus::Failed)->count(),
            'pending' => $this->importExports()->where('status', \App\Enums\ImportExportStatus::Pending)->count(),
            'processing' => $this->importExports()->where('status', \App\Enums\ImportExportStatus::Processing)->count(),
        ];
    }

    /**
     * Get artículos created by this user
     */
    public function articulos()
    {
        return $this->hasMany(Articulo::class);
    }

    /**
     * Configure activity logging
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'active', 'expires_at', 'require_2fa'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}")
            ->useLogName('user');
    }
}
