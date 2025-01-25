<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grade_level', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grade_id')
                ->constrained('grades')
                ->cascadeOnDelete();
            $table->foreignId('level_id')
                ->constrained('levels')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('grade_level');
        Schema::enableForeignKeyConstraints();
    }
};
