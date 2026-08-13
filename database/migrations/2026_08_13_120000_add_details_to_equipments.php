<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->string('capacity')->nullable()->after('serial_number');
            $table->text('specification')->nullable()->after('capacity');
            $table->smallInteger('manufacture_year')->nullable()->after('purchase_date');
            $table->string('condition')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropColumn(['capacity','specification','manufacture_year','condition']);
        });
    }
};
