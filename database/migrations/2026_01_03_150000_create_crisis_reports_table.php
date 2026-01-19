<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crisis_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crisis_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('urgency_level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('status')->default('new')->index();
            $table->string('verification_status')->default('pending')->index();
            $table->timestamp('occurred_at')->index();
            $table->string('address');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('description');
            $table->timestamps();

            $table->index(['crisis_type_id', 'urgency_level_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crisis_reports');
    }
};
