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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();

            // Core Identity
            $table->string('name');
            $table->string('short_name', 50)->nullable(); 
            $table->string('slug')->unique();
            $table->string('registration_number')->nullable();
            $table->string('motto')->nullable();
            $table->year('established_at')->nullable(); 

            // Contact Details
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable(); 
            $table->string('state', 100)->nullable(); 
            $table->string('pincode', 15)->nullable(); 
            $table->string('website')->nullable();

            // Branding & UI
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color', 10)->default('#4454c3');
            $table->string('principal_name')->nullable(); 
            $table->string('principal_signature')->nullable(); 

            // Financial/Bank Details
            $table->string('bank_name')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('upi_id')->nullable();
            $table->string('tax_id')->nullable(); 

            // Social Links
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable(); 
            // System Status
            $table->boolean('is_verified')->default(false);
            $table->boolean('status')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
