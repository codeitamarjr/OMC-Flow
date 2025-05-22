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
        Schema::create('cro_doc_definitions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('days_from_ard')->default(0);

            $table->boolean('is_global')->default(false);

            $table->foreignId('business_id')
                ->nullable()
                ->constrained('businesses')
                ->cascadeOnDelete();
        });

        Schema::create('company_cro_document', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->foreignId('cro_doc_definition_id')
                ->constrained('cro_doc_definitions')
                ->cascadeOnDelete();

            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users')->nullOnDelete();

            $table->unique(['company_id', 'cro_doc_definition_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cro_doc_definitions');
        Schema::dropIfExists('company_cro_document');
    }
};
