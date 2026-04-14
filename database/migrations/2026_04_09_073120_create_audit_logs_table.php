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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); 
            $table->unsignedBigInteger('affected_user_id')->nullable(); 
            $table->string('module');       

            $table->string('action');       
            $table->string('event')->nullable(); 
            $table->text('description')->nullable();

            // Record Tracking
            $table->unsignedBigInteger('record_id')->nullable();
            $table->string('field_name')->nullable();           

            $table->longText('old_value')->nullable();
            $table->longText('new_value')->nullable();

            $table->string('url')->nullable();
            $table->string('method', 10)->nullable(); 
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'module']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
