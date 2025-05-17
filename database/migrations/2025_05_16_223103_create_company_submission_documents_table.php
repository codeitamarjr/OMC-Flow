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
        Schema::create('company_submission_documents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('company_id')->constrained()->onDelete('cascade');

            $table->string('sub_num');
            $table->string('doc_num');
            $table->string('sub_type_desc')->nullable();
            $table->string('doc_type_desc')->nullable();
            $table->string('sub_status_desc')->nullable();

            $table->dateTime('sub_received_date')->nullable();
            $table->dateTime('sub_effective_date')->nullable();
            $table->dateTime('acc_year_to_date')->nullable();
            $table->dateTime('scan_date')->nullable();

            $table->integer('num_pages')->nullable();
            $table->bigInteger('doc_id')->nullable();
            $table->integer('file_size')->nullable();
            $table->boolean('scanned')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_submission_documents');
    }
};
