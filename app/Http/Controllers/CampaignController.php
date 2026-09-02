<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = trim((string) $request->input('search'));
        $campaigns = Campaign::with('owner')
            ->when($status, fn ($query, $value) => $query->where('status', $value))
            ->when($search !== '', fn ($query) => $query->where(fn ($inner) => $inner->where('name', 'like', '%' . $search . '%')->orWhere('channel', 'like', '%' . $search . '%')->orWhere('audience', 'like', '%' . $search . '%')))
            ->orderByRaw("FIELD(status, 'active', 'planned', 'paused', 'completed', 'archived')")
            ->orderByDesc('start_date')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();
        $summary = [
            'total' => Campaign::count(),
            'active' => Campaign::where('status', 'active')->count(),
            'planned' => Campaign::where('status', 'planned')->count(),
            'completed' => Campaign::where('status', 'completed')->count(),
        ];
        return view('campaigns.index', compact('campaigns', 'summary', 'status', 'search'));
    }

    public function create()
    {
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department']);
        return view('campaigns.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']) . '-' . Str::lower(Str::random(5));
        $data['created_by_user_id'] = auth()->id();
        $campaign = Campaign::create($data);
        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign berhasil dibuat.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['owner', 'creator', 'tasks.assignee']);
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department']);
        return view('campaigns.show', compact('campaign', 'users'));
    }

    public function edit(Campaign $campaign)
    {
        $users = User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department']);
        return view('campaigns.edit', compact('campaign', 'users'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        $campaign->update($this->validated($request));
        return redirect()->route('campaigns.show', $campaign)->with('success', 'Campaign berhasil diperbarui.');
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campaign dipindahkan ke Sampah Data.');
    }

    public function storeTask(Request $request, Campaign $campaign)
    {
        $data = $request->merge(['status' => $request->input('status', 'todo')]);
        $campaign->tasks()->create($this->validatedTask($data) + ['sort_order' => $campaign->tasks()->count()]);
        return back()->with('success', 'Task campaign berhasil ditambahkan.');
    }

    public function updateTask(Request $request, Campaign $campaign, CampaignTask $campaignTask)
    {
        abort_unless($campaignTask->campaign_id === $campaign->id, 404);
        $campaignTask->update($this->validatedTask($request));
        return back()->with('success', 'Task campaign berhasil diperbarui.');
    }

    public function destroyTask(Campaign $campaign, CampaignTask $campaignTask)
    {
        abort_unless($campaignTask->campaign_id === $campaign->id, 404);
        $campaignTask->delete();
        return back()->with('success', 'Task campaign berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'objective' => ['nullable', 'string'],
            'channel' => ['nullable', 'string', 'max:100'],
            'audience' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:planned,active,paused,completed,archived'],
            'owner_user_id' => ['nullable', 'exists:users,id'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'target_value' => ['nullable', 'numeric', 'min:0'],
            'achieved_value' => ['nullable', 'numeric', 'min:0'],
            'target_unit' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function validatedTask(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,done'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
            'assignee_id' => ['nullable', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
        ]);
    }
}
