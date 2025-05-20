<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyCRODocument;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CompanyCRODocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyCRODocument::factory()->count(10)->create();
    }
}
