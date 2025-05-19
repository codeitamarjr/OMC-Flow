<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Company;
use App\Models\Business;
use App\Models\UserNotificationSetting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
   
    public function run(): void
    {
        User::factory(100)->create()->each(function ($user) {
            Business::factory(2)->create()->each(function ($business) use ($user) {
                $business->users()->attach($user->id, ['role' => 'admin']);

                $user->update(['current_business_id' => $business->id]);

                Company::factory(100)->create([
                    'business_id' => $business->id,
                ]);
            });
        });

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->each(function ($user) {
            Business::factory(1)->create()->each(function ($business) use ($user) {
                $business->users()->attach($user->id, ['role' => 'admin']);

                $user->update(['current_business_id' => $business->id]);

                Company::factory(rand(40, 100))->create([
                    'business_id' => $business->id,
                ]);
            });
        });
    }
}
