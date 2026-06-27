<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'company',
        'designation',
        'user_role',
        'status',
        'plan_type',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'api_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
        'user_role' => UserRole::class,
    ];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function isActive(): bool
    {
        return (bool) $this->status;
    }

    public function isAdmin(): bool
    {
        return $this->user_role?->isAdmin() ?? false;
    }

    public function isCustomerView(): bool
    {
        return $this->usesSimplifiedLayout();
    }

    public function usesSimplifiedLayout(): bool
    {
        if ($this->email === config('digital-meter.customer_email')) {
            return true;
        }

        return $this->user_role === UserRole::Guest;
    }

    public function usesAdminLayout(): bool
    {
        return ! $this->usesSimplifiedLayout();
    }

    public function roleLabel(): string
    {
        return $this->user_role?->label() ?? UserRole::User->label();
    }

    public function loginRedirectUrl(): string
    {
        if (! $this->isAdmin()) {
            return route('devices.list', absolute: false);
        }

        return '/dashboard';
    }
}
