<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment_transfers', function (Blueprint $table) {
            $table->string('swap_group')->nullable()->index()->after('equipment_id');
        });
    }

    public function down(): void
    {
        Schema::table('equipment_transfers', function (Blueprint $table) {
            $table->dropColumn('swap_group');
        });
    }
};
