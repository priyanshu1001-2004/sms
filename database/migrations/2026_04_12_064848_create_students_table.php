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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');

            // Academic Details
            $table->string('admission_number')->unique();
            $table->string('roll_number')->nullable();
            $table->foreignId('class_id')->constrained('classes');
            $table->date('admission_date');
            $table->integer('user_id');
            $table->foreignId('parent_id')->nullable();

            // Personal Details
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('gender', 20); // Male, Female, Other
            $table->date('date_of_birth');
            $table->string('mobile_number')->nullable();
            $table->string('student_photo')->nullable();

            // Social & Health Details
            $table->string('religion')->nullable();
            $table->string('caste')->nullable();
            $table->string('blood_group', 10)->nullable();

            $table->boolean('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['organization_id', 'admission_number'], 'unique_admission_per_org');
            $table->unique(['organization_id', 'class_id', 'roll_number'], 'unique_roll_per_class');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
