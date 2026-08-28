<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_wastes', function (Blueprint $table) {
            $table->string('box_code', 100)->nullable()->after('equipment_id');
            $table->string('collection_status', 30)->default('collected')->after('box_code');
            $table->string('handover_recipient')->nullable()->after('handling_method');
            $table->date('handed_over_at')->nullable()->after('handover_recipient');
        });
    }

    public function down(): void
    {
        Schema::table('it_wastes', function (Blueprint $table) {
            $table->dropColumn(['box_code', 'collection_status', 'handover_recipient', 'handed_over_at']);
        });
    }
};
