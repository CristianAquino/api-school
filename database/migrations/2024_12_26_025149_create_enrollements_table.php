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
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('restrict');
            $table->foreignUuid('student_id')->constrained('students')->onDelete('restrict');
            $table->foreignId('grade_id')->constrained('grades')->onDelete('restrict');
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
