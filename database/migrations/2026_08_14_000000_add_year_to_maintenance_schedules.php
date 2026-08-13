<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            $table->smallInteger('year')->nullable()->after('month');
        });
    }

    public function down()
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            $table->dropColumn('year');
        });
    }
};
