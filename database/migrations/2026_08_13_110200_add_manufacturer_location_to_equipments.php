<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->foreignId('manufacturer_id')->nullable()->after('equipment_type_id')->constrained('manufacturers')->onDelete('set null');
            $table->foreignId('location_id')->nullable()->after('manufacturer_id')->constrained('locations')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manufacturer_id');
            $table->dropConstrainedForeignId('location_id');
        });
    }
};
