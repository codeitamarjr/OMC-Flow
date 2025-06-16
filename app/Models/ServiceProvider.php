<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceProviderFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'contact_name',
        'email',
        'phone',
        'website',
        'address',
        'notes',
    ];

    public function business(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ServiceCategory::class);
    }
}
