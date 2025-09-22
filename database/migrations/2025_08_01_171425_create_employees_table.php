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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Office::class);
            $table->string('avatar')->nullable();
            $table->string('employee_code')->unique();
            $table->string('name');
            $table->string('mobile')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('employment_type');
            $table->string('educational_qln')->nullable();
            $table->string('technical_qln')->nullable();
            $table->string('designation')->nullable();
            $table->string('post_assigned')->nullable();
            $table->string('name_of_workplace')->nullable();
            $table->string('post_per_qualification')->nullable();
            $table->date('date_of_engagement')->nullable();
            $table->string('skill_category')->nullable();
            $table->string('skill_at_present')->nullable();
            $table->string('engagement_card_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
