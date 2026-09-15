<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment_quarantines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->foreignId('quarantined_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('quarantined_at');
            $table->string('outcome', 30);
            $table->string('reason', 255);
            $table->text('notes')->nullable();
            $table->date('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment_quarantines');
    }
};
