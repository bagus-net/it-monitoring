<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$s = App\Models\User::documentSignatories();
foreach ($s as $key => $user) {
    $ok = $user && $user->signature_path && Illuminate\Support\Facades\Storage::disk('public')->exists($user->signature_path);
    echo str_pad($key, 14) . ': ' . ($user->name ?? 'TIDAK DITEMUKAN') . ' | ttd: ' . ($ok ? 'ADA' : 'TIDAK ADA') . PHP_EOL;
}

$view = 'resources/views/target_monitorings/index.blade.php';
$compiled = Illuminate\Support\Facades\Blade::compileString(file_get_contents(__DIR__ . '/' . $view));
$target = __DIR__ . '/storage/logs/compiled_check.php';
file_put_contents($target, $compiled);
exec('"C:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe" -l ' . escapeshellarg($target), $out, $code);
echo basename($view) . ' => ' . ($code === 0 ? 'OK' : 'ERROR: ' . implode(' ', $out)) . PHP_EOL;
