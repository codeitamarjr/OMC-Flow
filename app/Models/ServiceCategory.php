<?php

namespace App\Models;

use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ServiceCategoryFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'code',
        'name',
        'description',
    ];

    public function business(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function services(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ServiceProvider::class);
    }
}
