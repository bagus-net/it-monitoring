<?php

namespace App\Http\Controllers;

use App\Models\NetworkLink;
use App\Models\NetworkNode;
use App\Models\NetworkZone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NetworkTopologyController extends Controller
{
    private const NODE_TYPES = [
        'internet' => 'Internet / ISP',
        'router' => 'Router',
        'firewall' => 'Firewall',
        'core_switch' => 'Core Switch',
        'switch' => 'Switch',
        'access_point' => 'Access Point',
        'server' => 'Server',
        'other' => 'Lainnya',
    ];

    public function index(Request $request)
    {
        $nodes = NetworkNode::withCount(['outgoingLinks', 'incomingLinks'])->orderBy('type')->orderBy('name')->get();
        $links = NetworkLink::with(['source', 'target'])->latest()->get();
        $editingNode = $request->integer('edit_node') ? NetworkNode::find($request->integer('edit_node')) : null;
        $editingLink = $request->integer('edit_link') ? NetworkLink::find($request->integer('edit_link')) : null;
        $zones = NetworkZone::orderBy('name')->get();
        $summary = [
            'nodes' => $nodes->count(),
            'online' => $nodes->where('status', 'online')->count(),
            'offline' => $nodes->whereIn('status', ['offline', 'degraded'])->count(),
            'links' => $links->count(),
            'linksDown' => $links->where('status', 'down')->count(),
        ];
        $topologyNodes = $nodes->map(function ($node) {
            return [
                'id' => $node->id,
                'name' => $node->name,
                'type' => $node->type,
                'vendor' => $node->vendor,
                'zone' => $node->zone,
                'ip' => $node->ip_address,
                'status' => $node->status,
            ];
        })->values();
        $topologyLinks = $links->map(function ($link) {
            return [
                'id' => $link->id,
                'source' => $link->source_node_id,
                'target' => $link->target_node_id,
                'status' => $link->status,
                'bandwidth' => $link->bandwidth,
                'type' => $link->link_type,
            ];
        })->values();
        $nodeConnections = $nodes->mapWithKeys(function ($node) use ($links) {
            return [$node->id => $links->filter(function ($link) use ($node) {
                return $link->source_node_id === $node->id || $link->target_node_id === $node->id;
            })->map(function ($link) use ($node) {
                $peer = $link->source_node_id === $node->id ? $link->target : $link->source;
                return [
                    'id' => $link->id,
                    'peer' => $peer?->name ?? 'Node dihapus',
                    'media' => ucfirst($link->link_type),
                    'bandwidth' => $link->bandwidth ?: '-',
                    'status' => $link->status,
                ];
            })->values()];
        });

        return view('network.topology', compact('nodes', 'links', 'zones', 'topologyNodes', 'topologyLinks', 'nodeConnections', 'editingNode', 'editingLink', 'summary'));
    }

    public function storeNode(Request $request)
    {
        NetworkNode::create($this->validateNode($request));
        return back()->with('success', 'Perangkat jaringan berhasil ditambahkan.');
    }

    public function updateNode(Request $request, NetworkNode $networkNode)
    {
        $networkNode->update($this->validateNode($request));
        return back()->with('success', 'Perangkat jaringan berhasil diperbarui.');
    }

    public function destroyNode(NetworkNode $networkNode)
    {
        $networkNode->delete();
        return back()->with('success', 'Perangkat jaringan dan koneksinya berhasil dihapus.');
    }

    public function storeLink(Request $request)
    {
        $data = $request->validate([
            'source_node_id' => ['required', 'exists:network_nodes,id', 'different:target_node_id'],
            'target_node_id' => ['required', 'exists:network_nodes,id'],
            'link_type' => ['required', Rule::in(['ethernet', 'fiber', 'wireless', 'vpn', 'wan'])],
            'bandwidth' => ['nullable', 'string', 'max:80'],
            'status' => ['required', Rule::in(['up', 'down', 'degraded'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        NetworkLink::create($data);
        return back()->with('success', 'Koneksi jaringan berhasil ditambahkan.');
    }

    public function updateLink(Request $request, NetworkLink $networkLink)
    {
        $data = $request->validate([
            'source_node_id' => ['required', 'exists:network_nodes,id', 'different:target_node_id'],
            'target_node_id' => ['required', 'exists:network_nodes,id'],
            'link_type' => ['required', Rule::in(['ethernet', 'fiber', 'wireless', 'vpn', 'wan'])],
            'bandwidth' => ['nullable', 'string', 'max:80'],
            'status' => ['required', Rule::in(['up', 'down', 'degraded'])],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
        $networkLink->update($data);
        return back()->with('success', 'Koneksi jaringan berhasil diperbarui.');
    }

    public function destroyLink(NetworkLink $networkLink)
    {
        $networkLink->delete();
        return back()->with('success', 'Koneksi jaringan berhasil dihapus.');
    }

    public function storeZone(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:100', 'unique:network_zones,name'], 'description' => ['nullable', 'string', 'max:255'], 'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/']]);
        NetworkZone::create($data);
        return back()->with('success', 'Zona jaringan berhasil ditambahkan.');
    }

    public function destroyZone(NetworkZone $networkZone)
    {
        NetworkNode::where('zone', $networkZone->name)->update(['zone' => null]);
        $networkZone->delete();
        return back()->with('success', 'Zona jaringan berhasil dihapus.');
    }

    private function validateNode(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(self::NODE_TYPES))],
            'zone' => ['nullable', 'string', 'max:100', 'exists:network_zones,name'],
            'ip_address' => ['nullable', 'ip'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['online', 'offline', 'degraded', 'planned'])],
            'management_url' => ['nullable', 'url', 'max:500'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
