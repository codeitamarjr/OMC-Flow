<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('business_id')->constrained()->onDelete('cascade');

            $table->string('code')->unique(); // e.g. "CLEANING", "SECURITY", "BROADBAND"
            $table->string('name'); // e.g. "Carpet Cleaning","Security guarding", "Broadband Costs" ...
            $table->text('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
