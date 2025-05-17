<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanySubmissionDocument extends Model
{
    /** @use HasFactory<\Database\Factories\CompanySubmissionDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'sub_num',
        'doc_num',
        'sub_type_desc',
        'doc_type_desc',
        'sub_status_desc',
        'sub_received_date',
        'sub_effective_date',
        'acc_year_to_date',
        'scan_date',
        'num_pages',
        'doc_id',
        'file_size',
        'scanned',
    ];

    /**
     * Get the company that the submission document belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Returns the deadline for the submission document, based on the form type and other attributes.
     *
     * @return string|null The deadline as a date string, or null if the form type is not recognized.
     */
    public function getDeadlineAttribute(): ?string
    {
        $form = strtolower($this->document_type);

        $filingDate = $this->filing_date ?? $this->received_date ?? null;

        if (! $filingDate) return null;

        return match (true) {
            str_starts_with($form, 'form b1') || str_contains($form, 'annual return') => Carbon::parse($this->return_due_date)?->toDateString(),
            str_starts_with($form, 'form c1') || str_starts_with($form, 'form c6') => Carbon::parse($this->date_of_event)->addDays(21)->toDateString(),
            str_starts_with($form, 'form b10') || str_starts_with($form, 'form b2') => Carbon::parse($this->date_of_event)->addDays(14)->toDateString(),
            str_starts_with($form, 'form b5') => Carbon::parse($this->date_of_event)->addDays(30)->toDateString(),
            default => null,
        };
    }
}
