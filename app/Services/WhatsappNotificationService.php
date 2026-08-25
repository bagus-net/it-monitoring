<?php

namespace App\Services;

use App\Models\ItRepairTicket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/** Pengirim notifikasi WhatsApp via gateway Fonnte saat ada tiket perbaikan IT baru. */
class WhatsappNotificationService
{
    public function notifyNewTicket(ItRepairTicket $ticket): void
    {
        if (!config('services.whatsapp.enabled')) {
            return;
        }

        $targets = config('services.whatsapp.targets', []);
        if (empty($targets)) {
            return;
        }

        $equipmentName = $ticket->equipment->name ?? 'Peralatan belum dipilih';
        $message = "*Tiket Perbaikan IT Baru*\n"
            . "No. Tiket: {$ticket->ticket_number}\n"
            . "Pelapor: {$ticket->reported_by}\n"
            . "Bagian: {$ticket->department}\n"
            . "Peralatan: {$equipmentName}\n"
            . "Masalah: {$ticket->problem_description}\n"
            . "Prioritas: " . strtoupper($ticket->priority ?? '-') . "\n"
            . "Waktu: " . optional($ticket->reported_at)->format('d M Y H:i');

        $this->send(implode(',', $targets), $message);
    }

    private function send(string $target, string $message): void
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders(['Authorization' => config('services.whatsapp.token')])
                ->asForm()
                ->post(config('services.whatsapp.api_url'), [
                    'target' => $target,
                    'message' => $message,
                ]);

            if (!$response->successful()) {
                Log::warning('Notifikasi WhatsApp gagal terkirim', ['status' => $response->status(), 'body' => $response->body()]);
            }
        } catch (\Throwable $e) {
            Log::warning('Notifikasi WhatsApp error: ' . $e->getMessage());
        }
    }
}
