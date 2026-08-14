<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->onDelete('cascade');
            $table->smallInteger('year');
            $table->tinyInteger('month');
            $table->date('checked_at');
            $table->string('reported_by')->nullable();
            $table->string('acknowledged_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['checklist_item_id', 'year', 'month'], 'maintenance_checklist_period_idx');
        });

        Schema::create('maintenance_checklist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_checklist_id')->constrained('maintenance_checklists')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->enum('result', ['ok', 'not_ok']);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['maintenance_checklist_id', 'equipment_id'], 'maintenance_checklist_equipment_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_checklist_entries');
        Schema::dropIfExists('maintenance_checklists');
    }
};
