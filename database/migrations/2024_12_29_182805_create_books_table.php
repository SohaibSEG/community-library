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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->string('cover_image')->nullable();
            $table->text('description')->nullable();
            $table->string('genre')->nullable();
            $table->year('published_year')->nullable();
            $table->enum('condition', ['New', 'Good', 'Fair'])->default('Good');
            $table->enum('status', ['Available', 'Lent'])->default('Available');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Foreign key
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
