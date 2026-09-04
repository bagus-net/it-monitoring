<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IsoDocumentAdminController extends Controller
{
    public function editAllowedUsers(Request $request)
    {
        abort_unless($request->user()->isMaster(), 403);
        $users = User::where('is_active', true)->orderBy('name')->get();
        $allowedIds = DB::table('iso_document_creators')->pluck('user_id')->toArray();

        return view('iso_documents.allowed_users', compact('users', 'allowedIds'));
    }

    public function updateAllowedUsers(Request $request)
    {
        abort_unless($request->user()->isMaster(), 403);
        $data = $request->validate([
            'allowed_user_ids' => 'nullable|array',
            'allowed_user_ids.*' => 'exists:users,id',
        ]);

        DB::transaction(function () use ($data) {
            DB::table('iso_document_creators')->delete();
            $now = now();
            $rows = [];
            foreach ($data['allowed_user_ids'] ?? [] as $uid) {
                $rows[] = ['user_id' => $uid, 'created_at' => $now, 'updated_at' => $now];
            }
            if (!empty($rows)) {
                DB::table('iso_document_creators')->insert($rows);
            }
        });

        return redirect()->route('iso-documents.allowed-creators.edit')->with('success', 'Daftar pengguna yang boleh membuat folder ISO berhasil diperbarui.');
    }
}
