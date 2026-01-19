<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('crisis_reports', 'verification_status')) {
            Schema::table('crisis_reports', function (Blueprint $table) {
                $table->string('verification_status')->default('pending')->index()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('crisis_reports', 'verification_status')) {
            Schema::table('crisis_reports', function (Blueprint $table) {
                $table->dropIndex(['verification_status']);
                $table->dropColumn('verification_status');
            });
        }
    }
};
