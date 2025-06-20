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
        Schema::create('contract_reminders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('company_service_contract_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->date('due_date'); // first/next due date
            $table->enum('frequency', ['manual','weekly', 'biweekly', 'semimonthly', 'monthly', 'bimonthly', 'threemonthly', 'quarterly', 'yearly', 'once'])->nullable();

            $table->unsignedTinyInteger('day_of_month')->nullable(); // e.g., 15th of the month
            $table->json('months_active')->nullable(); // e.g., ["January", "February", "March"] or ["1", "2", "3"] for Jan, Feb, Mar
            $table->json('custom_dates')->nullable(); // e.g., ["2025-02-12", "2025-05-08", "2025-11-30"]

            $table->integer('reminder_days_before')->default(0);
            $table->integer('reminder_days_after')->default(0);
            $table->boolean('notified_before')->default(false);
            $table->boolean('notified_after')->default(false);
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_reminders');
    }
};
