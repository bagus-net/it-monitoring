<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->foreignId('equipment_owner_user_id')->nullable()->after('equipment_id')->constrained('users')->nullOnDelete();
            $table->string('equipment_owner_name')->nullable()->after('equipment_owner_user_id');
            $table->string('equipment_owner_department')->nullable()->after('equipment_owner_name');
        });
    }

    public function down(): void
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('equipment_owner_user_id');
            $table->dropColumn(['equipment_owner_name', 'equipment_owner_department']);
        });
    }
};
