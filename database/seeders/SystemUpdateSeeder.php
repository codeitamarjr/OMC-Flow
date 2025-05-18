<?php

namespace Database\Seeders;

use App\Models\SystemUpdate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SystemUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SystemUpdate::factory()->count(10)->create();
    }
}
