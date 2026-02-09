<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crisis_handlings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crisis_report_id')->unique()->constrained('crisis_reports')->cascadeOnDelete();
            $table->string('status')->default('BARU')->index();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('current_note')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crisis_handlings');
    }
};
