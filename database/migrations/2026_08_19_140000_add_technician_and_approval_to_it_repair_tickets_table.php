<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->foreignId('technician_id')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->after('technician_id')->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down()
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('technician_id');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn('approved_at');
        });
    }
};
