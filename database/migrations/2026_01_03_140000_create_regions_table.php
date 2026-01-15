<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('regions')
                ->nullOnDelete();
            $table->enum('level', ['province', 'city', 'district', 'village']);
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->timestamps();

            $table->index(['level', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regions');
    }
};
