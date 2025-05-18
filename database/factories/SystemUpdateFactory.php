<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SystemUpdate>
 */
class SystemUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'version' => $this->faker->uuid,
            'commit_title' => $this->faker->sentence,
            'commit_description' => $this->faker->paragraph,
            'update_log' => $this->faker->paragraph,
            'status' => 'successful',
        ];
    }
}
