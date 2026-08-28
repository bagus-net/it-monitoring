<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            $table->foreignId('reported_by_user_id')->nullable()->after('reported_by')->constrained('users')->nullOnDelete();
            $table->foreignId('acknowledged_by_user_id')->nullable()->after('acknowledged_by')->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable()->after('acknowledged_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('maintenance_checklists', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reported_by_user_id');
            $table->dropConstrainedForeignId('acknowledged_by_user_id');
            $table->dropColumn('acknowledged_at');
        });
    }
};
