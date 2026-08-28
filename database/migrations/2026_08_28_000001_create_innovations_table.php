<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('innovations', function (Blueprint $table) {
            $table->id();
            $table->date('innovation_date');
            $table->string('title');
            $table->text('implementation')->nullable();
            $table->date('implementation_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('paper_path')->nullable();
            $table->string('paper_name')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('innovations');
    }
};
