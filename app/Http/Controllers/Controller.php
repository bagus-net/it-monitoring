<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /** `per_page=all` dipakai halaman ekspor agar seluruh data tampil dalam satu halaman. */
    protected function resolvePerPage(Request $request, int $default = 50): int
    {
        return $request->input('per_page') === 'all' ? 100000 : $default;
    }
}
