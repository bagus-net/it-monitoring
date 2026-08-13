<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->foreignId('checklist_item_id')->nullable()->constrained('checklist_items')->onDelete('set null');
            $table->timestamp('performed_at')->nullable();
            $table->string('performed_by')->nullable();
            $table->enum('result', ['ok','needs_repair','n/a'])->default('ok');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('maintenance_logs');
    }
};
