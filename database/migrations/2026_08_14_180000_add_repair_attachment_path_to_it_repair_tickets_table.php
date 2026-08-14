<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->string('repair_attachment_path')->nullable()->after('repair_action');
        });
    }

    public function down()
    {
        Schema::table('it_repair_tickets', function (Blueprint $table) {
            $table->dropColumn('repair_attachment_path');
        });
    }
};
