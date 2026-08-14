<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('web_monitoring_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('sites')->onDelete('cascade');
            $table->dateTime('checked_at');
            $table->string('checked_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('web_monitoring_checklist_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('web_monitoring_checklist_id');
            $table->foreign('web_monitoring_checklist_id', 'web_mon_check_entry_fk')
                ->references('id')->on('web_monitoring_checklists')->onDelete('cascade');
            $table->string('item_code');
            $table->string('item_name');
            $table->enum('result', ['pass', 'fail', 'na'])->default('pass');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('web_monitoring_checklist_entries');
        Schema::dropIfExists('web_monitoring_checklists');
    }
};
