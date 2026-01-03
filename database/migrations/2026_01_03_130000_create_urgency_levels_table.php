<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urgency_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->unsignedTinyInteger('level');
            $table->string('color', 20)->nullable();
            $table->boolean('is_high_priority')->default(false);
            $table->timestamps();

            $table->unique('level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urgency_levels');
    }
};
