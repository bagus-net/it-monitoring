<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignTask;
use App\Models\User;
use Illuminate\Http\Request;

class TodoListController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $priority = $request->input('priority');
        $search = trim((string) $request->input('search'));
        $tasks = CampaignTask::with(['campaign', 'assignee'])
            ->when($status, fn ($query, $value) => $query->where('status', $value))
            ->when($priority, fn ($query, $value) => $query->where('priority', $value))
            ->when($search !== '', fn ($query) => $query->where(fn ($inner) => $inner->where('title', 'like', '%' . $search . '%')->orWhereHas('campaign', fn ($campaign) => $campaign->where('name', 'like', '%' . $search . '%'))))
            ->orderByRaw("FIELD(status, 'in_progress', 'todo', 'done')")
            ->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
            ->orderBy('due_date')
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();
        $summary = ['total' => CampaignTask::count(), 'todo' => CampaignTask::where('status', 'todo')->count(), 'in_progress' => CampaignTask::where('status', 'in_progress')->count(), 'done' => CampaignTask::where('status', 'done')->count()];
        return view('todo_list.index', compact('tasks', 'summary', 'status', 'priority', 'search'));
    }

    public function create()
    {
        return view('todo_list.create', ['campaigns' => Campaign::orderBy('name')->get(['id', 'name']), 'users' => $this->users()]);
    }

    public function store(Request $request)
    {
        CampaignTask::create($this->validated($request));
        return redirect()->route('todo-list.index')->with('success', 'To-do berhasil ditambahkan.');
    }

    public function edit(CampaignTask $todoList)
    {
        return view('todo_list.edit', ['task' => $todoList, 'campaigns' => Campaign::orderBy('name')->get(['id', 'name']), 'users' => $this->users()]);
    }

    public function update(Request $request, CampaignTask $todoList)
    {
        $todoList->update($this->validated($request));
        return redirect()->route('todo-list.index')->with('success', 'To-do berhasil diperbarui.');
    }

    public function destroy(CampaignTask $todoList)
    {
        $todoList->delete();
        return back()->with('success', 'To-do berhasil dihapus.');
    }

    private function users()
    {
        return User::where('is_active', true)->orderBy('name')->get(['id', 'name', 'department']);
    }

    private function validated(Request $request): array
    {
        return $request->validate(['campaign_id' => ['required', 'exists:campaigns,id'], 'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string'], 'status' => ['required', 'in:todo,in_progress,done'], 'priority' => ['required', 'in:low,normal,high,urgent'], 'assignee_id' => ['nullable', 'exists:users,id'], 'due_date' => ['nullable', 'date']]);
    }
}
