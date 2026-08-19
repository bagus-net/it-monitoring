<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['master', 'admin_it', 'user'])->default('user')->after('email');
            $table->string('department')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('department');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department', 'is_active']);
        });
    }
};
