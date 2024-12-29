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
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade'); // Foreign key
            $table->foreignId('borrower_id')->constrained('users')->onDelete('cascade'); // Foreign key
            $table->foreignId('lender_id')->constrained('users')->onDelete('cascade'); // Foreign key
            $table->foreignId('lending_id')->unique()->nullable()->constrained('lendings')->onDelete('cascade'); // Foreign key
            $table->integer('no_of_days');
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
