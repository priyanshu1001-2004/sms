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
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');

            $table->string('name'); // e.g., A+, A, B, Fail
            $table->decimal('percent_from', 5, 2); // e.g., 80.00
            $table->decimal('percent_to', 5, 2);   // e.g., 89.99
            $table->decimal('grade_point', 3, 2);  // e.g., 4.00 (for GPA)

            $table->string('remarks')->nullable(); // e.g., Excellent, Good
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
