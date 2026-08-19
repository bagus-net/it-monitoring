<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\WebMonitoringChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebMonitoringChecklistController extends Controller
{
    private const CHECKLIST_ITEMS = [
        'security' => [
            ['code' => 'SSL_TLS', 'name' => 'Sertifikat SSL/TLS valid dan tidak mendekati masa kedaluwarsa.'],
            ['code' => 'HTTPS_REDIRECT', 'name' => 'Akses HTTP dialihkan ke HTTPS sesuai kebijakan.'],
            ['code' => 'ACCESS_CONTROL', 'name' => 'Akses pengguna dan hak otorisasi berfungsi sesuai peran.'],
            ['code' => 'ACCOUNT_SECURITY', 'name' => 'Akun admin tidak menggunakan kredensial default dan akun tidak aktif ditinjau.'],
            ['code' => 'VULNERABILITY', 'name' => 'Patch keamanan aplikasi, CMS, plugin, dan dependensi ditinjau.'],
            ['code' => 'BACKUP_SECURITY', 'name' => 'Backup data tersedia dan akses backup dibatasi.'],
            ['code' => 'LOG_AUDIT', 'name' => 'Log keamanan dan audit akses tersedia untuk ditinjau.'],
            ['code' => 'ERROR_DISCLOSURE', 'name' => 'Pesan error tidak membocorkan informasi sensitif.'],
        ],
        'functional' => [
            ['code' => 'AVAILABILITY', 'name' => 'Layanan web dapat diakses (HTTP/HTTPS).'],
            ['code' => 'HTTP_STATUS', 'name' => 'Kode respons HTTP sesuai (2xx atau redirect yang disetujui).'],
            ['code' => 'RESPONSE_TIME', 'name' => 'Waktu respons layanan berada pada batas yang dapat diterima.'],
            ['code' => 'DNS', 'name' => 'Nama domain/DNS berhasil di-resolve dengan benar.'],
            ['code' => 'HOME_PAGE', 'name' => 'Halaman utama memuat konten tanpa error tampilan.'],
            ['code' => 'LOGIN_FUNCTION', 'name' => 'Fungsi login/logout berjalan sesuai proses bisnis.'],
            ['code' => 'CRITICAL_FUNCTION', 'name' => 'Fungsi kritis (transaksi, form, atau pencarian) dapat dijalankan.'],
            ['code' => 'DATA_PROCESSING', 'name' => 'Data dapat disimpan, ditampilkan, dan diperbarui dengan benar.'],
            ['code' => 'REPORT_PRINT', 'name' => 'Fungsi laporan atau cetak dokumen berjalan bila berlaku.'],
            ['code' => 'ALERTING', 'name' => 'Log/error dan notifikasi insiden dapat dipantau.'],
        ],
    ];

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $checklists = WebMonitoringChecklist::with('site')
            ->withCount('entries')
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%' . $search . '%';
                $query->where(function ($inner) use ($keyword) {
                    $inner->where('checked_by', 'like', $keyword)
                        ->orWhere('checklist_type', 'like', $keyword)
                        ->orWhere('notes', 'like', $keyword)
                        ->orWhereHas('site', fn ($relation) => $relation->where('name', 'like', $keyword)->orWhere('url', 'like', $keyword));
                });
            })
            ->orderByDesc('checked_at')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();
        $summary = [
            'total' => WebMonitoringChecklist::count(),
            'security' => WebMonitoringChecklist::where('checklist_type', 'security')->count(),
            'functional' => WebMonitoringChecklist::where('checklist_type', 'functional')->count(),
        ];

        return view('web_monitoring_checklists.index', compact('checklists', 'summary', 'search'));
    }

    public function create()
    {
        $sites = Site::orderBy('name')->get();
        $itemsByType = self::CHECKLIST_ITEMS;

        return view('web_monitoring_checklists.create', compact('sites', 'itemsByType'));
    }

    public function store(Request $request)
    {
        $data = $this->validateChecklist($request);

        $checklist = DB::transaction(function () use ($data) {
            $checklist = WebMonitoringChecklist::create([
                'site_id' => $data['site_id'],
                'checklist_type' => $data['checklist_type'],
                'checked_at' => $data['checked_at'],
                'checked_by' => $data['checked_by'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
            foreach ($data['entries'] as $entry) {
                $checklist->entries()->create($entry);
            }
            return $checklist;
        });

        return redirect()->route('web-monitoring-checklists.show', $checklist)->with('success', 'Checklist Web Monitoring berhasil disimpan.');
    }

    public function show(WebMonitoringChecklist $webMonitoringChecklist)
    {
        $webMonitoringChecklist->load(['site', 'entries']);
        $summary = [
            'pass' => $webMonitoringChecklist->entries->where('result', 'pass')->count(),
            'fail' => $webMonitoringChecklist->entries->where('result', 'fail')->count(),
            'na' => $webMonitoringChecklist->entries->where('result', 'na')->count(),
        ];

        return view('web_monitoring_checklists.show', compact('webMonitoringChecklist', 'summary'));
    }

    public function destroy(WebMonitoringChecklist $webMonitoringChecklist)
    {
        $webMonitoringChecklist->delete();

        return redirect()->route('web-monitoring-checklists.index')->with('success', 'Checklist Web Monitoring berhasil dihapus.');
    }

    private function validateChecklist(Request $request): array
    {
        return $request->validate([
            'site_id' => 'required|exists:sites,id',
            'checklist_type' => 'required|in:security,functional',
            'checked_at' => 'required|date',
            'checked_by' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'entries' => 'required|array|min:1',
            'entries.*.item_code' => 'required|string|max:100',
            'entries.*.item_name' => 'required|string|max:255',
            'entries.*.result' => 'required|in:pass,fail,na',
            'entries.*.remarks' => 'nullable|string',
        ]);
    }
}
