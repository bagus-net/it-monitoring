<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_waste_batches', function (Blueprint $table) {
            $table->id();
            $table->string('box_code', 100)->unique();
            $table->date('opened_at');
            $table->string('storage_location')->nullable();
            $table->string('status', 30)->default('open');
            $table->string('handover_recipient')->nullable();
            $table->date('handed_over_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('it_wastes', function (Blueprint $table) {
            $table->foreignId('it_waste_batch_id')->nullable()->after('equipment_id')->constrained('it_waste_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('it_wastes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('it_waste_batch_id');
        });
        Schema::dropIfExists('it_waste_batches');
    }
};
