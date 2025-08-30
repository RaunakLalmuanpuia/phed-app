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
        Schema::create('transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Employee::class);
            $table->foreignId('current_office_id')->constrained('offices')->onDelete('cascade');
            $table->foreignId('requested_office_id')->constrained('offices')->onDelete('cascade');
            $table->date('request_date')->nullable();
            $table->string('approval_status')->default('Pending');
            $table->date('approval_date')->nullable();
            $table->string('supporting_document')->nullable(); // File path
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_requests');
    }
};
