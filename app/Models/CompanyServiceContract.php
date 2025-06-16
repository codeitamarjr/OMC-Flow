<?php

namespace App\Models;

use App\Models\Company;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class CompanyServiceContract extends Model
{
    protected $fillable = [
        'company_id',
        'service_provider_id',
        'service_category_id',
        'budget',
        'start_date',
        'next_due_date',
        'status',
        'notes',
    ];

    public function company(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function provider(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'service_provider_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }
}
