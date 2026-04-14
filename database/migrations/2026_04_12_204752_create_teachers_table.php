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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            // Multi-tenancy & User Link
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            // Personal Information
            $table->string('teacher_photo')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender', 20);
            $table->date('date_of_birth');
            $table->string('blood_group', 10)->nullable();
            $table->string('marital_status')->nullable(); // Single, Married, etc.

            // Contact Information
            $table->string('email')->unique();
            $table->string('mobile_number');
            $table->string('emergency_contact_number')->nullable();

            // Address
            $table->text('current_address');
            $table->text('permanent_address')->nullable();

            // Professional Details
            $table->string('qualification');
            $table->string('work_experience')->nullable(); // e.g., "5 Years"
            $table->string('designation')->default('Teacher');
            $table->date('date_of_joining');

            // Documents & Payroll (Important for SaaS)
            $table->string('pan_number')->nullable();
            $table->string('epf_number')->nullable(); // Employee Provident Fund
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->string('resume_path')->nullable(); // Uploaded file

            // Metadata
            $table->text('note')->nullable();
            $table->boolean('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            // Indices
            $table->unique(['organization_id', 'email'], 'unique_teacher_email_per_org');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
