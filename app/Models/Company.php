<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\CompanyCRODocument;
use App\Models\CompanyServiceContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'custom',
        'company_number',
        'company_type',
        'status',
        'active',
        'effective_date',
        'registration_date',
        'last_annual_return',
        'next_annual_return',
        'next_financial_statement_due',
        'last_accounts',
        'last_agm',
        'financial_year_end',
        'postcode',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'address_line_4',
        'place_of_business',
        'company_type_code',
        'company_status_code',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'registration_date' => 'date',
        'last_annual_return' => 'date',
        'next_annual_return' => 'date',
        'next_financial_statement_due' => 'date',
        'last_accounts' => 'date',
        'last_agm' => 'date',
        'financial_year_end' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Eloquent event hook for when a Company is created.
     *
     * When a Company is created, this method retrieves all current CroDocDefinition
     * IDs and associates them with the newly created Company, setting the completed
     * status to false for each association. The operation is performed without
     * detaching existing associations.
     */
    protected static function booted()
    {
        static::created(function (Company $company) {
            $defs = CroDocDefinition::all()->pluck('id');
            if ($defs->isNotEmpty()) {
                $company->croDocDefinitions()
                    ->syncWithoutDetaching(
                        $defs->mapWithKeys(fn($id) => [$id => ['completed' => false]])
                            ->toArray()
                    );
            }
        });
    }

    /**
     * Get the business that the company belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the tags that the company has been assigned.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get the submission documents associated with the company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submissionDocuments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CompanySubmissionDocument::class);
    }

    /**
     * Returns the Annual Return status of the company.
     *
     * The status is determined based on the difference between the current date
     * and the next annual return date. If the difference is negative, the company
     * is overdue. If the difference is 30 days or less, the company is due soon.
     * Otherwise, the company is compliant.
     *
     * @return string One of "Overdue", "Due Soon", or "Compliant".
     */
    public function getArStatusAttribute(): string
    {
        if (!$this->next_annual_return) {
            return 'Unknown';
        }

        $dueDate = Carbon::parse($this->next_annual_return);
        $diff = now()->diffInDays($dueDate, false);

        return match (true) {
            $diff < 0 => 'Overdue',
            $diff <= 56 => 'Due Soon',
            default => 'Compliant',
        };
    }

    /**
     * Returns the CRO document definitions associated with the company.
     *
     * Each CRO document definition is associated with a pivot row in the
     * company_cro_document table which contains the completed and completed_at
     * timestamps.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function croDocDefinitions()
    {
        return $this->belongsToMany(
            CroDocDefinition::class,
            'company_cro_document',
            'company_id',
            'cro_doc_definition_id'
        )
            ->withPivot(['completed', 'completed_at', 'completed_by'])
            ->withTimestamps();
    }

    /**
     * Returns the number of incomplete CRO document definitions for the company.
     *
     * @return int The number of incomplete CRO document definitions.
     */
    public function getCroIncompleteCountAttribute(): int
    {
        return $this->croDocDefinitions
            ->where('pivot.completed', false)
            ->count();
    }

    public function contracts()
    {
        return $this->hasMany(CompanyServiceContract::class);
    }
}
