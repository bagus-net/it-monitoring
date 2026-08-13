<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            $table->foreignId('checklist_item_id')->nullable()->after('equipment_id')->constrained('checklist_items')->onDelete('set null');
            $table->tinyInteger('week_of_month')->nullable()->after('month');
        });
    }

    public function down()
    {
        Schema::table('maintenance_schedules', function (Blueprint $table) {
            $table->dropForeign(['checklist_item_id']);
            $table->dropColumn('checklist_item_id');
            $table->dropColumn('week_of_month');
        });
    }
};
