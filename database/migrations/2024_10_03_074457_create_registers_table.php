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
        Schema::create('registers', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->uuid('device_id');
            $table->integer('parent_id')->default(0);
            $table->string('title');
            $table->string('value')->nullable();
            $table->string('unit')->nullable();
            $table->string('type')->nullable();
            $table->string('scale')->nullable();
            $table->enum('input', ['digital', 'analog', 'none'])->default('none');
            $table->enum('output', ['digital', 'analog', 'none'])->default('none');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registers');
    }
};
