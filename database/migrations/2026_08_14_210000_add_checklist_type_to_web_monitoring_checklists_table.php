<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('web_monitoring_checklists', function (Blueprint $table) {
            $table->enum('checklist_type', ['security', 'functional'])->default('functional')->after('site_id');
        });
    }

    public function down()
    {
        Schema::table('web_monitoring_checklists', function (Blueprint $table) {
            $table->dropColumn('checklist_type');
        });
    }
};
