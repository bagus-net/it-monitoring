<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iso_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number', 50)->unique();
            $table->string('title');
            $table->string('category', 100);
            $table->string('revision', 30)->nullable();
            $table->date('document_date')->nullable();
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->foreignId('created_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('iso_document_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iso_document_id')->constrained('iso_documents')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['iso_document_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iso_document_user');
        Schema::dropIfExists('iso_documents');
    }
};
