<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\UserNotificationSetting;

class NotificationPreferences extends Component
{
    public array $preferences = [];

    public function mount()
    {
        $user = Auth::user();

        foreach (UserNotificationSetting::KEYS as $key => $meta) {
            $pref = UserNotificationSetting::firstOrCreate(
                ['user_id' => $user->id, 'notification_key' => $key],
                ['is_enabled' => true]
            );

            $this->preferences[$key] = [
                'label'       => $meta['label'],
                'description' => $meta['description'],
                'is_enabled'  => $pref->is_enabled,
            ];
        }
    }

    /**
     * Toggle the given notification preference on or off.
     *
     * @param string $key
     */
    public function toggle(string $key)
    {
        $user = Auth::user();

        $pref = UserNotificationSetting::where([
            ['user_id', $user->id],
            ['notification_key', $key],
        ])->firstOrFail();

        $pref->is_enabled = ! $pref->is_enabled;
        $pref->save();

        $this->preferences[$key]['is_enabled'] = $pref->is_enabled;
    }


    public function render()
    {
        return view('livewire.settings.notification-preferences');
    }
}
