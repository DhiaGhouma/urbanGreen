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

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_login_at',
        // preferences is set explicitly, not mass-assigned, so not mandatory here
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
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'preferences' => 'array',
        ];
    }

    /**
     * Get the participations for the user.
     */
    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function participationFeedbacks()
    {
        return $this->hasMany(ParticipationFeedback::class);
    }

    /**
     * Get the event registrations for the user.
     */
    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }
    /**
     * Check if the user account is currently locked
     */
    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Lock the user account for a specified duration (in minutes)
     */
    public function lockAccount(int $minutes = 15): void
    {
        $this->update([
            'locked_until' => now()->addMinutes($minutes),
        ]);
    }

    /**
     * Unlock the user account
     */
    public function unlockAccount(): void
    {
        $this->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
        ]);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_login_attempts');
        
        // Lock account after 5 failed attempts
        if ($this->failed_login_attempts >= 5) {
            $this->lockAccount(15); // 15 minutes lockout
        }
    }

    /**
     * Reset failed login attempts
     */
    public function resetFailedAttempts(): void
    {
        $this->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
        ]);
    }

    /**
     * Update last login timestamp
     */
    public function updateLastLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
        ]);
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a moderator
     */
    public function isModerator(): bool
    {
        return $this->hasRole('moderator') || $this->isAdmin();
    }

    /**
     * Get the user's display role
     */
    public function getRoleDisplayAttribute(): string
    {
        return ucfirst($this->role);
    }
}
