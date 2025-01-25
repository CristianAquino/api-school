<?php

use App\Models\Qualification;
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
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->decimal('number_note',  4, 2);
            $table->enum('letter_note', Qualification::LETTER_NOTES);
            $table->decimal('avg', 4, 2);
            $table->foreignUuid('student_id')
                ->constrained('students')
                ->cascadeOnDelete();
            $table->foreignId('course_id')
                ->constrained('courses')
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
        Schema::dropIfExists('qualifications');
        Schema::enableForeignKeyConstraints();
    }
};
