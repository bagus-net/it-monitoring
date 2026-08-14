<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('it_repair_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('equipment_id')->nullable()->constrained('equipments')->onDelete('set null');
            $table->string('department')->nullable();
            $table->dateTime('reported_at');
            $table->text('problem_description');
            $table->text('repair_action')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('status', ['open', 'in_progress', 'resolved'])->default('open');
            $table->string('reported_by')->nullable();
            $table->string('assigned_to')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'priority'], 'it_repair_ticket_status_priority_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('it_repair_tickets');
    }
};
