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
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_id'); // Snapshot of class at time of exam

            // Scoring Data
            $table->decimal('marks_obtained', 5, 2)->default(0.00);
            $table->string('attendance')->default('P'); // P = Present, A = Absent, M = Medical

            // Calculated Data (Cache)
            $table->string('grade_name')->nullable(); // e.g., A+
            $table->decimal('grade_point', 3, 2)->nullable(); // e.g., 4.0

            $table->text('teacher_remarks')->nullable();
            $table->unsignedBigInteger('created_by'); // Teacher/Admin who entered marks

            $table->timestamps();

            // Foreign keys
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
