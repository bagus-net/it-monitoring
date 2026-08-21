<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use App\Models\CctvConnection;
use App\Models\NetworkZone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CctvController extends Controller
{
    private const STATUSES = ['active' => 'Aktif', 'offline' => 'Offline', 'maintenance' => 'Maintenance', 'inactive' => 'Nonaktif'];
    private const TYPES = ['ip_camera' => 'IP Camera', 'dvr' => 'DVR', 'nvr' => 'NVR', 'ptz' => 'PTZ Camera', 'analog' => 'Analog Camera', 'other' => 'Lainnya'];

    public function index(Request $request)
    {
        $status = $request->input('status');
        $zone = $request->input('zone');
        $editing = $request->integer('edit') ? Cctv::find($request->integer('edit')) : null;
        $cctvs = Cctv::with(['networkZone', 'recorderConnections.camera'])
            ->when($status, fn ($query, $value) => $query->where('status', $value))
            ->when($zone, fn ($query, $value) => $query->where('network_zone_id', $value))
            ->orderBy('name')->paginate($this->resolvePerPage($request))->withQueryString();
        $previewCctvs = Cctv::with(['networkZone', 'recorderConnections.camera'])->orderBy('network_zone_id')->orderBy('name')->get();
        $recorders = Cctv::whereIn('camera_type', ['nvr', 'dvr'])->orderBy('name')->get();
        $cameraOptions = Cctv::whereNotIn('camera_type', ['nvr', 'dvr'])->orderBy('name')->get();
        $connections = CctvConnection::with(['recorder', 'camera'])->latest()->get();
        $topologyNodes = $previewCctvs->map(fn ($camera) => [
            'id' => $camera->id,
            'name' => $camera->name,
            'code' => $camera->code,
            'type' => $camera->camera_type,
            'status' => $camera->status,
        ])->values();
        $topologyLinks = $connections->map(fn ($connection) => [
            'source' => $connection->recorder_id,
            'target' => $connection->camera_id,
            'channel' => $connection->channel,
            'status' => $connection->status,
        ])->values();
        $zones = NetworkZone::orderBy('name')->get();
        $summary = [
            'total' => Cctv::count(),
            'active' => Cctv::where('status', 'active')->count(),
            'attention' => Cctv::whereIn('status', ['offline', 'maintenance'])->count(),
            'zones' => Cctv::whereNotNull('network_zone_id')->distinct('network_zone_id')->count('network_zone_id'),
        ];
        $selectedZone = $zone;
        return view('cctv.index', compact('cctvs', 'previewCctvs', 'topologyNodes', 'topologyLinks', 'recorders', 'cameraOptions', 'connections', 'zones', 'editing', 'summary', 'status', 'zone', 'selectedZone'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCctv($request);
        $data['code'] = $this->nextCode();
        Cctv::create($data);
        return redirect()->route('cctv.index')->with('success', 'CCTV berhasil ditambahkan.');
    }

    public function update(Request $request, Cctv $cctv)
    {
        $data = $this->validateCctv($request, $cctv);
        unset($data['code']);
        if (!$request->filled('password')) unset($data['password']);
        if (!$request->filled('stream_url')) unset($data['stream_url']);
        $cctv->update($data);
        return redirect()->route('cctv.index')->with('success', 'CCTV berhasil diperbarui.');
    }

    public function toggleStatus(Cctv $cctv)
    {
        $cctv->update(['status' => $cctv->status === 'active' ? 'inactive' : 'active']);
        return back()->with('success', 'Status CCTV berhasil diperbarui.');
    }

    public function destroy(Cctv $cctv)
    {
        $cctv->delete();
        return back()->with('success', 'CCTV berhasil dihapus.');
    }

    public function storeConnection(Request $request)
    {
        $data = $request->validate([
            'recorder_id' => ['required', 'exists:cctvs,id'],
            'camera_id' => ['required', 'exists:cctvs,id', 'different:recorder_id'],
            'channel' => ['nullable', 'string', 'max:50'],
            'status' => ['required', Rule::in(['connected', 'offline', 'degraded'])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
        $recorder = Cctv::findOrFail($data['recorder_id']);
        abort_unless(in_array($recorder->camera_type, ['nvr', 'dvr'], true), 422, 'Perangkat sumber harus NVR atau DVR.');
        CctvConnection::create($data);
        return back()->with('success', 'Koneksi NVR/DVR ke kamera berhasil ditambahkan.');
    }

    public function destroyConnection(CctvConnection $cctvConnection)
    {
        $cctvConnection->delete();
        return back()->with('success', 'Koneksi CCTV berhasil dihapus.');
    }

    private function validateCctv(Request $request, ?Cctv $cctv = null): array
    {
        return $request->validate([
            'code' => ['nullable', 'string', 'max:50', Rule::unique('cctvs', 'code')->ignore($cctv?->id)],
            'name' => ['required', 'string', 'max:255'],
            'camera_type' => ['required', Rule::in(array_keys(self::TYPES))],
            'brand' => ['nullable', 'string', 'max:100'], 'model' => ['nullable', 'string', 'max:100'],
            'ip_address' => ['nullable', 'ip'], 'web_url' => ['nullable', 'url', 'max:500'],
            'stream_url' => ['nullable', 'string', 'max:2000'], 'username' => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'max:255'], 'network_zone_id' => ['nullable', 'exists:network_zones,id'],
            'location_detail' => ['nullable', 'string', 'max:255'], 'status' => ['required', Rule::in(array_keys(self::STATUSES))],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function nextCode(): string
    {
        $number = Cctv::count() + 1;
        do { $code = 'CCTV-' . str_pad((string) $number++, 4, '0', STR_PAD_LEFT); } while (Cctv::where('code', $code)->exists());
        return $code;
    }
}
