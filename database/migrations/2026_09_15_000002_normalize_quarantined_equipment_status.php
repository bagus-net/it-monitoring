<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('equipments')
            ->whereIn('id', DB::table('equipment_quarantines')->whereNull('released_at')->pluck('equipment_id'))
            ->update(['status' => 'nonaktif']);
    }

    public function down(): void
    {
        // The previous status is not safely recoverable from quarantine history.
    }
};
