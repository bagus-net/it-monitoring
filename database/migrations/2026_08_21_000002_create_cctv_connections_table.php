<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cctv_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recorder_id')->constrained('cctvs')->cascadeOnDelete();
            $table->foreignId('camera_id')->constrained('cctvs')->cascadeOnDelete();
            $table->string('channel')->nullable();
            $table->string('status')->default('connected');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['recorder_id', 'camera_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctv_connections');
    }
};
