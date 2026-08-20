<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sparepart_transactions', function (Blueprint $table) {
            $table->foreignId('equipment_id')->nullable()->after('sparepart_type_id')->constrained('equipments')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sparepart_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('equipment_id');
        });
    }
};
