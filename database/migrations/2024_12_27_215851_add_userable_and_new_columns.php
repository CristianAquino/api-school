<?php

use App\Models\User;
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
        //
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 32)->nullable();
            $table->string('second_name', 32)->nullable();
            $table->string('phone', 9)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address', 128)->nullable();
            $table->string('dni', 8)->nullable();
            $table->string('code')->unique();
            $table->string('userable_id')->nullable();
            $table->string('userable_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('second_name');
            $table->dropColumn('phone');
            $table->dropColumn('birth_date');
            $table->dropColumn('address');
            $table->dropColumn('dni');
            $table->dropColumn('userable_id');
            $table->dropColumn('userable_type');
        });
    }
};