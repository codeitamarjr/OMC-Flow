<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemUpdate extends Model
{
    /** @use HasFactory<\Database\Factories\SystemUpdateFactory> */
    use HasFactory;

      protected $fillable = [
        'version',
        'commit_title',
        'commit_description',
        'update_log',
        'status',
    ];
}
