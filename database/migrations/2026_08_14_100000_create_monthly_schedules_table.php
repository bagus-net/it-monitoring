<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('monthly_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_item_id')->constrained('checklist_items')->onDelete('cascade');
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->tinyInteger('month'); // 1-12
            $table->smallInteger('year');
            $table->json('dates')->nullable(); // Array of dates [1,2,5,10,etc]
            $table->string('notes')->nullable();
            $table->timestamps();

            // Unique constraint: one schedule per equipment per month
            // Explicit short name: default generated name exceeds MySQL's 64-char identifier limit
            $table->unique(['checklist_item_id', 'equipment_id', 'month', 'year'], 'monthly_sched_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('monthly_schedules');
    }
};
