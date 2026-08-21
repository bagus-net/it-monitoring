<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cctvs', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('camera_type')->default('ip_camera');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('web_url')->nullable();
            $table->text('stream_url')->nullable();
            $table->string('username')->nullable();
            $table->text('password')->nullable();
            $table->foreignId('network_zone_id')->nullable()->constrained('network_zones')->nullOnDelete();
            $table->string('location_detail')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('last_checked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cctvs');
    }
};
