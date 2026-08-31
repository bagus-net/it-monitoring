<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('target_monitorings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedTinyInteger('month');
            $table->string('metric_key', 50);
            $table->unsignedInteger('value')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('updated_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['year', 'month', 'metric_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('target_monitorings');
    }
};