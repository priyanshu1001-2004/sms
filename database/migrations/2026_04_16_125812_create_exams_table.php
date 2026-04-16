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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('academic_year_id'); // e.g., 2025-26 session

            $table->string('name'); // e.g., First Term Examination
            $table->string('term_name')->nullable(); // e.g., Semester 1

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->text('description')->nullable();

            // Professional toggles
            $table->boolean('is_published')->default(false); // Result visibility
            $table->boolean('status')->default(true); // Active/Inactive

            $table->timestamps();
            $table->softDeletes();

            // Foreign keys for data integrity
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
            $table->foreign('academic_year_id')->references('id')->on('academic_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
