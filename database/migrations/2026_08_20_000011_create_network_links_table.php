<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_node_id')->constrained('network_nodes')->cascadeOnDelete();
            $table->foreignId('target_node_id')->constrained('network_nodes')->cascadeOnDelete();
            $table->string('link_type')->default('ethernet');
            $table->string('bandwidth')->nullable();
            $table->string('status')->default('up');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_links');
    }
};
