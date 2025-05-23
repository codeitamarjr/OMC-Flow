<?php

namespace App\Models;

use App\Models\Business;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
     * The business that owns this definition (nullable for globals).
     */
    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the companies associated with the CRO document definition.
     *
     * This relationship is defined through the pivot table 'company_cro_document',
     * and includes additional pivot data: 'completed' status and 'completed_at' timestamp.
     * The timestamps for the pivot table are also maintained.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(
            Company::class,
            'company_cro_document',
            'cro_doc_definition_id',
            'company_id'
        )
            ->withPivot(['completed', 'completed_at', 'completed_by'])
            ->withTimestamps();
    }

    /**
     * Only return definitions this business can see:
     *  – its own (business_id = $businessId)
     *  – or global ones (is_global = true)
     */
    public function scopeAccessible(Builder $query, int $businessId): Builder
    {
        return $query->where(function ($q) use ($businessId) {
            $q->where('business_id', $businessId)
                ->orWhere('is_global', true);
        });
    }

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
            if ($def->business) {
                $def->business->companies()
                    ->chunk(100, function ($companies) use ($def) {
                        $companies->each(
                            fn(Company $company) =>
                            $company->croDocDefinitions()
                                ->syncWithoutDetaching([
                                    $def->id => ['completed' => false],
                                ])
                        );
                    });
            }
        });

        static::deleting(function (CroDocDefinition $def) {
            $def->companies()->detach();
        });
    }
}
