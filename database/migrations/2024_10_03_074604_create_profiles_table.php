<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birthday')->nullable();
            $table->text('address')->nullable();
            $table->string('language')->default('en');
            $table->string('timezone')->default('Asia/Tehran');
            $table->enum('calendar', ['Gregorian', 'Jalali', 'Hijri'])->default('Gregorian');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
