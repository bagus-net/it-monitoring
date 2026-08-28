<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('it_wastes', function (Blueprint $table) {
            $table->string('waste_code', 30)->nullable()->unique()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('it_wastes', function (Blueprint $table) {
            $table->dropUnique(['waste_code']);
            $table->dropColumn('waste_code');
        });
    }
};
