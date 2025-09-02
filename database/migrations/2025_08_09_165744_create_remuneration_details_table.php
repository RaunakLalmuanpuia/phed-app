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
        Schema::create('remuneration_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->decimal('remuneration', 10, 2);
            $table->string('pay_matrix')->nullable();
            $table->decimal('medical_percentage', 5, 2);
            $table->decimal('medical_amount', 10, 2);
            $table->decimal('total', 10, 2);
            $table->decimal('round_total', 10, 2);
            $table->date('next_increment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remuneration_details');
    }
};
