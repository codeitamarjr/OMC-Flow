<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CompanyCRODocument extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyCRODocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'code',
        'description',
        'days_from_ard',
        'completed',
        'completed_at',
        'completed_by',
    ];

    protected $casts = [
        'completed'    => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * The company this document requirement belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Calculate the due date by adding days_from_ard to the company's
     * next_annual_return date.
     */
    public function getDueDateAttribute(): ?Carbon
    {
        if (! $this->company || ! $this->company->next_annual_return) {
            return null;
        }

        return Carbon::parse($this->company->next_annual_return)
            ->addDays($this->days_from_ard);
    }

    /**
     * Get the user that owns the company CRO document.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
