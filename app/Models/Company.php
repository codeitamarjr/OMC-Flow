<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
        'name',
        'company_number',
        'company_type',
        'status',
        'effective_date',
        'registration_date',
        'last_annual_return',
        'next_annual_return',
        'next_financial_statement_due',
        'last_accounts',
        'postcode',
        'address_line_1',
        'address_line_2',
        'address_line_3',
        'address_line_4',
        'place_of_business',
        'company_type_code',
        'company_status_code',
    ];

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
}
