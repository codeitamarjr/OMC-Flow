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
        Schema::create('company_obligation_reminders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cro_doc_definition_id')->constrained('cro_doc_definitions')->cascadeOnDelete();

            $table->string('reminder_type');
            $table->date('trigger_date');
            $table->date('due_date');
            $table->timestamp('sent_at')->nullable();

            $table->unique(
                ['user_id', 'company_id', 'cro_doc_definition_id', 'reminder_type', 'trigger_date'],
                'company_obligation_reminders_unique'
            );
            $table->index(['trigger_date', 'reminder_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_obligation_reminders');
    }
};

