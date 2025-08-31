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
        Schema::create('document_edit_request_files', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\DocumentEditRequest::class);
            $table->foreignIdFor(\App\Models\DocumentType::class);
            $table->string('mime');
            $table->text('path');
            $table->string('name');
            $table->string('type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_edit_request_files');
    }
};
