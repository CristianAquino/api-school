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
        Schema::create('enrollements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('academic_year_id')
                ->nullable()
                ->constrained('academic_years')
                ->onDelete('set null');
            $table->foreignUuid('student_id')
                ->nullable()
                ->constrained('students')
                ->cascadeOnDelete();
            $table->foreignId('grade_level_id')
                ->nullable()
                ->constrained('grade_level')
                ->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('enrollements');
        Schema::enableForeignKeyConstraints();
    }
};
