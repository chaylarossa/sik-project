<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('handling_updates')) {
        Schema::create('handling_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('crisis_reports')->cascadeOnDelete();
            $table->foreignId('updated_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->index();
            $table->unsignedTinyInteger('progress_percent');
            $table->text('note')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();

            $table->index(['report_id', 'status']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('handling_updates');
    }
};
