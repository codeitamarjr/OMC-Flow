<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CroDocDefinition extends Model
{
    /** @use HasFactory<\Database\Factories\CroDocDefinitionFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'days_from_ard',
        'is_global',
        'business_id',
    ];

    /**
     * Eloquent event hook for when a CroDocDefinition is created or deleted.
     *
     * When a CroDocDefinition is created, we need to attach it to all existing companies
     * and set the completed status to false. When a CroDocDefinition is deleted, we
     * simply detach it from all companies.
     *
     * We chunk the companies in batches of 100, as this is a large and potentially
     * expensive operation.
     */
    protected static function booted()
    {
        static::created(function (CroDocDefinition $def) {
            if ($def->is_global) {
                \App\Models\Company::chunk(100, function ($companies) use ($def) {
                    $companies->each(
                        fn($company) =>
                        $company->croDocDefinitions()
                            ->syncWithoutDetaching([$def->id => ['completed' => false]])
                    );
                });
            } else {
                // Optionally, for non-global ...
            }
        });

        static::deleting(function (CroDocDefinition $def) {
            $def->companies()->detach();
        });
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company_cro_document')
            ->withPivot(['completed', 'completed_at'])
            ->withTimestamps();
    }
}
