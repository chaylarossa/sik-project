<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crisis_report_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crisis_report_id')->constrained('crisis_reports')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['crisis_report_id', 'unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crisis_report_unit');
    }
};
