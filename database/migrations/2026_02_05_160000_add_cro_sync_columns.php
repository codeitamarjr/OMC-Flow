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
        Schema::table('companies', function (Blueprint $table) {
            $table->json('cro_officers_snapshot')->nullable()->after('company_status_code');
            $table->timestamp('cro_officers_synced_at')->nullable()->after('cro_officers_snapshot');
        });

        Schema::table('company_cro_document', function (Blueprint $table) {
            $table->date('due_date')->nullable()->after('completed_by');
            $table->date('last_filed_at')->nullable()->after('due_date');
            $table->string('status')->default('missing')->after('last_filed_at');
            $table->string('risk_level')->default('medium')->after('status');
            $table->string('notes')->nullable()->after('risk_level');

            $table->index(['status', 'risk_level']);
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company_cro_document', function (Blueprint $table) {
            $table->dropIndex(['status', 'risk_level']);
            $table->dropIndex(['due_date']);

            $table->dropColumn([
                'due_date',
                'last_filed_at',
                'status',
                'risk_level',
                'notes',
            ]);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'cro_officers_snapshot',
                'cro_officers_synced_at',
            ]);
        });
    }
};
