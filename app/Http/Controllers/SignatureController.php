<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{
    public function edit()
    {
        return view('profile.signature', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'signature_title' => ['nullable', 'string', 'max:100'],
            'signature_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'signature_data' => ['nullable', 'string'],
        ]);

        $user = $request->user();
        $path = null;

        if ($request->hasFile('signature_file')) {
            $path = $request->file('signature_file')->store('signatures', 'public');
        } elseif ($request->filled('signature_data')) {
            $path = $this->storeDrawnSignature($request->input('signature_data'), $user->id);
        }

        if ($path) {
            if ($user->signature_path) {
                Storage::disk('public')->delete($user->signature_path);
            }
            $user->signature_path = $path;
        }

        $user->signature_title = $request->input('signature_title');
        $user->save();

        return redirect()->route('signature.edit')->with('success', 'Tanda tangan digital berhasil disimpan.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if ($user->signature_path) {
            Storage::disk('public')->delete($user->signature_path);
            $user->signature_path = null;
            $user->save();
        }

        return redirect()->route('signature.edit')->with('success', 'Tanda tangan digital dihapus.');
    }

    /** Kanvas tanda tangan dikirim sebagai data URL PNG. */
    private function storeDrawnSignature(string $dataUrl, int $userId): ?string
    {
        if (!preg_match('/^data:image\/png;base64,/', $dataUrl)) {
            return null;
        }

        $binary = base64_decode(substr($dataUrl, strlen('data:image/png;base64,')), true);
        if ($binary === false || strlen($binary) > 2 * 1024 * 1024) {
            return null;
        }

        $path = 'signatures/ttd-' . $userId . '-' . now()->format('YmdHis') . '.png';
        Storage::disk('public')->put($path, $binary);

        return $path;
    }
}
