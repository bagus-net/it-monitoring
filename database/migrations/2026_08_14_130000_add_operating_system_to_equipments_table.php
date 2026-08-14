<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->string('operating_system')->nullable()->after('model');
        });
    }

    public function down()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropColumn('operating_system');
        });
    }
};
