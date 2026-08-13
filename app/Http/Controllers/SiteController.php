<?php

namespace App\Http\Controllers;

use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'url' => 'required|string|max:255',
        ]);

        $url = trim($data['url']);
        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.$url;
        }

        $site = Site::create([
            'name' => $data['name'] ?: $url,
            'url' => $url,
            'is_active' => true,
        ]);

        return response()->json(['ok' => true, 'site_id' => $site->id]);
    }

    public function destroy(Site $site): JsonResponse
    {
        $site->delete();

        return response()->json(['ok' => true]);
    }

    public function toggle(Site $site): JsonResponse
    {
        $site->update(['is_active' => ! $site->is_active]);

        return response()->json(['ok' => true, 'active' => $site->is_active]);
    }
}
