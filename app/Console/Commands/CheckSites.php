<?php

namespace App\Console\Commands;

use App\Services\SiteMonitorService;
use Illuminate\Console\Command;

class CheckSites extends Command
{
    protected $signature = 'monitor:check';

    protected $description = 'Cek status & response time semua situs yang aktif dipantau';

    public function handle(SiteMonitorService $monitor): int
    {
        $this->info('Mengecek situs...');
        $monitor->checkAll();
        $this->info('Selesai.');

        return self::SUCCESS;
    }
}
