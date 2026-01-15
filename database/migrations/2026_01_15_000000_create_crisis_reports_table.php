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
            $table->foreignId('crisis_type_id')->constrained('crisis_types');
            $table->foreignId('urgency_level_id')->constrained('urgency_levels');
            $table->unsignedBigInteger('region_id')->index();
            $table->dateTime('occurred_at');
            $table->text('description');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('address_text');
            $table->foreignId('created_by')->constrained('users');
            $table->string('verification_status', 20)->default('pending');
            $table->string('handling_status', 20)->default('new');
            $table->timestamps();

            $table->index(['crisis_type_id', 'urgency_level_id']);
            $table->index(['verification_status', 'handling_status']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crisis_reports');
    }
};
