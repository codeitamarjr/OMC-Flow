<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompanyCRODocument>
 */
class CompanyCRODocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->company(),
            'code' => $this->faker->uuid,
            'description' => $this->faker->text,
            'days_from_ard' => $this->faker->numberBetween(1, 365),
            'completed' => $this->faker->boolean,
            'created_at' => now(),
        ];
    }
}
