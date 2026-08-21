<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('network_nodes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('switch');
            $table->string('zone')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('vendor')->nullable();
            $table->string('status')->default('online');
            $table->string('management_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('network_nodes');
    }
};
