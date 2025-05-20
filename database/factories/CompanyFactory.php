<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'business_id' => \App\Models\Business::factory(),
            'name' => 'OMC ' . $this->faker->company,
            'custom' => null,
            'company_number' => strtoupper($this->faker->unique()->bothify('CRO###??###')),
            'company_type' => 'Private',
            'status' => 'Active',
            'effective_date' => now(),
            'registration_date' => now()->subYears(rand(1, 10)),
            'last_annual_return' => now()->subYear(),
            'next_annual_return' => now()->addYear(),
            'next_financial_statement_due' => now()->addMonths(rand(1, 12)),
            'last_accounts' => now()->subYear(),
            'postcode' => $this->faker->postcode,
            'address_line_1' => $this->faker->streetAddress,
            'address_line_2' => $this->faker->secondaryAddress,
            'address_line_3' => $this->faker->city,
            'address_line_4' => $this->faker->state,
            'place_of_business' => $this->faker->city,
            'company_type_code' => $this->faker->numberBetween(1000, 9999),
            'company_status_code' => $this->faker->numberBetween(1000, 9999),
        ];
    }
}
