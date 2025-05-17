<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

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
        'current_business_id',
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

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Checks if the user is the owner of a given business
     *
     * @param int $businessId The ID of the business to check
     * @return bool Whether the user owns the business
     */
    public function ownsBusiness($businessId): bool
    {
        return $this->businesses->contains('id', $businessId);
    }

    /**
     * Get the businesses that the user belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */

    public function businesses(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Business::class)->withPivot('role')->withTimestamps();
    }

    /**
     * The current business that the user is using.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currentBusiness(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Business::class, 'current_business_id');
    }

    /**
     * Check if the user's current business has any companies assigned.
     *
     * @return bool Whether the user's current business has any companies assigned.
     */
    public function hasCompaniesInCurrentBusiness(): bool
    {
        $business = $this->currentBusiness()->first();
        return $business && $business->companies()->exists();
    }

    /**
     * Get the role of the user in their current business.
     *
     * @return string|null The user's role in the current business, or null if not found.
     */

    public function roleInCurrentBusiness(): ?string
    {
        return $this->businesses()
            ->where('business_id', $this->current_business_id)
            ->first()?->pivot?->role;
    }

    /**
     * Get the role of the user in the given business.
     *
     * @param int $businessId The ID of the business to check
     * @return string|null The user's role in the business, or null if not found.
     */
    public function roleInBusiness($businessId): ?string
    {
        return $this->businesses()
            ->where('business_id', $businessId)
            ->first()?->pivot?->role;
    }
}
