<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->string('equipment_category')->nullable()->after('equipment_id');
            $table->string('error_type')->nullable()->after('equipment_category');
        });
    }

    public function down()
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->dropColumn(['equipment_category', 'error_type']);
        });
    }
};
