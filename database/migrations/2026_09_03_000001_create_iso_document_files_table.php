<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iso_document_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('iso_document_id')->constrained('iso_documents')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->foreignId('uploaded_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Allow folders (iso_documents) to exist without a single top-level file now that files live in iso_document_files.
        DB::statement('ALTER TABLE iso_documents MODIFY file_path VARCHAR(255) NULL');
        DB::statement('ALTER TABLE iso_documents MODIFY file_name VARCHAR(255) NULL');

        // Move existing single-file documents into the new files table so nothing already uploaded is lost.
        DB::table('iso_documents')->whereNotNull('file_path')->orderBy('id')->get()->each(function ($document) {
            DB::table('iso_document_files')->insert([
                'iso_document_id' => $document->id,
                'file_path' => $document->file_path,
                'file_name' => $document->file_name,
                'uploaded_by_user_id' => $document->created_by_user_id,
                'created_at' => $document->created_at,
                'updated_at' => $document->updated_at,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iso_document_files');
        DB::statement('ALTER TABLE iso_documents MODIFY file_path VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE iso_documents MODIFY file_name VARCHAR(255) NOT NULL');
    }
};
