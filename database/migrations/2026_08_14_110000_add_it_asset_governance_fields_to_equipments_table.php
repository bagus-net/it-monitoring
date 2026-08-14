<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->string('asset_tag')->nullable()->unique()->after('name');
            $table->string('model')->nullable()->after('serial_number');
            $table->string('vendor_name')->nullable()->after('manufacturer_id');
            $table->string('owner_name')->nullable()->after('location_id');
            $table->string('department')->nullable()->after('owner_name');
            $table->string('criticality')->nullable()->after('condition');
            $table->date('warranty_expiry')->nullable()->after('purchase_date');
            $table->date('support_contract_end')->nullable()->after('warranty_expiry');
        });
    }

    public function down()
    {
        Schema::table('equipments', function (Blueprint $table) {
            $table->dropUnique(['asset_tag']);
            $table->dropColumn([
                'asset_tag',
                'model',
                'vendor_name',
                'owner_name',
                'department',
                'criticality',
                'warranty_expiry',
                'support_contract_end',
            ]);
        });
    }
};
