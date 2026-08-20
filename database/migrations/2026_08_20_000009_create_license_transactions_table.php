<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_type_id')->constrained('license_types')->cascadeOnDelete();
            $table->enum('type', ['purchase', 'assign', 'release', 'renew']);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedInteger('seats_before');
            $table->unsignedInteger('seats_after');
            $table->date('transaction_date');
            $table->foreignId('equipment_id')->nullable()->constrained('equipments')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_transactions');
    }
};
