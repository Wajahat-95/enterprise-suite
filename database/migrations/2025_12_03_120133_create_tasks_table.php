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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            // Field 1: Task Owner (Foreign Key to the users table)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // Field 2: Task Title
            $table->string('title');
            // Field 3: Task Status (defaulting to false, meaning incomplete)
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
