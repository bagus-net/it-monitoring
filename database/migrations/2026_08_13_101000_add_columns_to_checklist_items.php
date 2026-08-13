<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->enum('frequency', ['monthly','annual','both'])->default('monthly')->after('description');
            $table->string('applicable_months')->nullable()->after('frequency')->comment('comma-separated month numbers e.g. 1,4,7');
        });
    }

    public function down()
    {
        Schema::table('checklist_items', function (Blueprint $table) {
            $table->dropColumn(['frequency','applicable_months']);
        });
    }
};
