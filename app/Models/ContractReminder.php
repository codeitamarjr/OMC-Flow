<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_service_contract_id',
        'title',
        'due_date', // first/next due date
        'frequency', // manual, monthly, bimonthly, quarterly, yearly, once
        'day_of_month', // e.g., 15th of the month
        'months_active', // e.g., ["January", "February", "March"] or ["1", "2", "3"] for Jan, Feb, Mar
        'custom_dates', // e.g., ["2025-02-12", "2025-05-08", "2025-11-30"]
        'reminder_days_before', // days before the due date to notify
        'reminder_days_after', // days after the due date to notify
        'notified_before', // whether notification before the due date has been sent
        'notified_after', // whether notification after the due date has been sent
        'notes', // additional notes for the reminder
    ];

    protected $casts = [
        'custom_dates' => 'array',
        'months_active' => 'array',
    ];

    public function contract()
    {
        return $this->belongsTo(CompanyServiceContract::class, 'company_service_contract_id');
    }
}
