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
        Schema::create('system_updates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('version'); // Short SHA or tag
            $table->string('commit_title');
            $table->text('commit_description')->nullable();
            $table->longText('update_log');
            $table->enum('status', ['pending', 'running', 'successful', 'failed'])->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_updates');
    }
};
