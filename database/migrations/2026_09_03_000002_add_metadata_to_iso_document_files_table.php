<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iso_document_files', function (Blueprint $table) {
            if (!Schema::hasColumn('iso_document_files', 'file_size')) {
                $table->unsignedBigInteger('file_size')->nullable()->after('file_name');
            }
            if (!Schema::hasColumn('iso_document_files', 'uploaded_by_user_id')) {
                $table->foreignId('uploaded_by_user_id')->nullable()->after('file_size')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('iso_document_files', function (Blueprint $table) {
            $table->dropConstrainedForeignId('uploaded_by_user_id');
            $table->dropColumn('file_size');
        });
    }
};
