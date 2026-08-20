<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('vendor')->nullable();
            $table->string('license_key')->nullable();
            $table->unsignedInteger('total_seats')->default(1);
            $table->unsignedInteger('used_seats')->default(0);
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_types');
    }
};
