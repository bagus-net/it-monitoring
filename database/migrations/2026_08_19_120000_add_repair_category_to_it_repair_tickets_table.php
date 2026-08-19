<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->enum('repair_category', ['hardware', 'software'])->default('hardware')->after('equipment_id');
            $table->string('software_name')->nullable()->after('repair_category');
        });
    }

    public function down()
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->dropColumn(['repair_category', 'software_name']);
        });
    }
};
