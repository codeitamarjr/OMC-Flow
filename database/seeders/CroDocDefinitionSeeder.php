<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CroDocDefinition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CroDocDefinitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CroDocDefinition::create([
            'name'             => 'Annual Return',
            'code'             => 'B1',
            'description'      => 'Must be filed within 56 days of the “Return Made Up To” date.',
            'days_from_ard'    => 56,
            'is_global'        => true,
            'business_id' => null,
        ]);
    }
}
