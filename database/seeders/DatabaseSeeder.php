<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Company;
use App\Models\Business;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(100)->create()->each(function ($user) {
            // Each user creates 2 businesses
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
            // Each user creates 1 business
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
