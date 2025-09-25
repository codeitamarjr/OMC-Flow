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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('business_id')->constrained()->onDelete('cascade');

            $table->string('name');
            $table->string('custom')->nullable();
            $table->string('company_number')->unique();
            $table->string('company_type');
            $table->string('status')->default('Active');
            $table->boolean('active')->default(true)->index();

            $table->date('effective_date')->nullable();
            $table->date('registration_date')->nullable();
            $table->date('last_annual_return')->nullable();
            $table->date('next_annual_return')->nullable();
            $table->date('next_financial_statement_due')->nullable();
            $table->date('last_accounts')->nullable();
            $table->date('last_agm')->nullable();
            $table->date('financial_year_end')->nullable();

            $table->string('postcode')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('address_line_4')->nullable();

            $table->string('place_of_business')->nullable();
            $table->integer('company_type_code')->nullable();
            $table->integer('company_status_code')->nullable();

            $table->index(['business_id', 'name']);
            $table->index(['business_id', 'custom']);
            $table->index(['business_id', 'company_number']);
            $table->index(['business_id', 'next_annual_return']);
            $table->index(['business_id', 'last_agm']);
            $table->index(['business_id', 'financial_year_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
