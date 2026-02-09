<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crisis_handling_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crisis_handling_id')->constrained('crisis_handlings')->cascadeOnDelete();
            $table->string('type')->index(); // ASSIGNMENT, PROGRESS, STATUS
            $table->json('payload');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->nullable(); // For Eloquent consistency
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crisis_handling_logs');
    }
};
