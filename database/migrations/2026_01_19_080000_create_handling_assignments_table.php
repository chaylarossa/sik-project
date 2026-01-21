<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handling_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('crisis_reports')->cascadeOnDelete();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('assignee_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('active')->index();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['report_id', 'unit_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handling_assignments');
    }
};
