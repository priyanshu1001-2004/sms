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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('parent_photo')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender', 20);
            $table->string('occupation')->nullable();

            $table->string('email');
            $table->string('mobile_number')->nullable();
            $table->string('alternate_number')->nullable();
            $table->text('address')->nullable();

            $table->string('relation')->comment('Father, Mother, Guardian');
            $table->string('national_id')->nullable(); 

            $table->boolean('status')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['organization_id', 'email'], 'unique_parent_email_per_org');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};