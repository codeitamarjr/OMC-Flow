<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserNotificationSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserNotificationSetting>
 */
class UserNotificationSettingFactory extends Factory
{
    protected $model = UserNotificationSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $notificationKeys = array_keys(UserNotificationSetting::KEYS);

        return [
            'user_id' => User::factory(),
            'notification_key' => $this->faker->randomElement($notificationKeys),
            'is_enabled' => $this->faker->boolean(),
        ];
    }
}
