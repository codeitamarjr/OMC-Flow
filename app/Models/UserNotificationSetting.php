<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotificationSetting extends Model
{
    /** @use HasFactory<\Database\Factories\UserNotificationSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notification_key',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    /**
     * The different notification “keys” the app supports.
     */
    public const KEYS = [
        'annual_return_status' => [
            'label'       => 'Annual Return Status',
            'description' => 'Notifies you when a company’s next annual return is overdue or due soon.',
        ],
        'document_deadlines' => [
            'label'       => 'Document Deadlines',
            'description' => 'Get a heads-up before any document submission deadlines approach.',
        ],
        'overdue_escalation' => [
            'label'       => 'Overdue Escalation',
            'description' => 'Receive escalation alerts when filing obligations become overdue.',
        ],
        // add more as you go...
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
