<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BackupController extends Controller
{
    public function index()
    {
        $tables = $this->tableNames();
        $publicFiles = Storage::disk('public')->allFiles();

        return view('settings.backup', [
            'tableCount' => count($tables),
            'fileCount' => count($publicFiles),
        ]);
    }

    public function download(): BinaryFileResponse
    {
        abort_unless(class_exists(ZipArchive::class), 503, 'Ekstensi ZIP PHP belum aktif.');

        $timestamp = now()->format('Y-m-d_His');
        $backupDirectory = storage_path('app/backups');
        File::ensureDirectoryExists($backupDirectory);
        $archivePath = $backupDirectory . DIRECTORY_SEPARATOR . 'it-monitoring-backup-' . $timestamp . '.zip';

        $archive = new ZipArchive();
        abort_unless($archive->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true, 500, 'File backup tidak dapat dibuat.');

        $database = [
            'application' => config('app.name'),
            'database' => config('database.connections.' . config('database.default') . '.database'),
            'created_at' => now()->toIso8601String(),
            'tables' => [],
        ];

        foreach ($this->tableNames() as $table) {
            $database['tables'][$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
        }

        $archive->addFromString('database.json', json_encode($database, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE));
        $archive->addFromString('database.sql', $this->buildSqlDump());
        $archive->addFromString('README.txt', "Backup IT Monitoring\n\nIsi:\n- database.sql: struktur dan seluruh data database, dapat dipulihkan melalui MySQL/phpMyAdmin.\n- database.json: seluruh data tabel dalam format JSON.\n- storage/: seluruh file pada storage/app/public.\n\nFile .env tidak disertakan demi keamanan.\n");

        $publicStoragePath = storage_path('app/public');
        foreach (Storage::disk('public')->allFiles() as $file) {
            $absolutePath = $publicStoragePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $file);
            if (is_file($absolutePath)) {
                $archive->addFile($absolutePath, 'storage/' . $file);
            }
        }

        $archive->close();

        return response()->download($archivePath, basename($archivePath), [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    private function tableNames(): array
    {
        $databaseName = config('database.connections.' . config('database.default') . '.database');
        $column = 'Tables_in_' . $databaseName;

        return collect(DB::select('SHOW TABLES'))
            ->map(fn ($table) => $table->{$column} ?? array_values((array) $table)[0] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    private function buildSqlDump(): string
    {
        $pdo = DB::connection()->getPdo();
        $lines = [
            '-- IT Monitoring database backup',
            '-- Created: ' . now()->toIso8601String(),
            'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";',
            'SET FOREIGN_KEY_CHECKS = 0;',
            '',
        ];

        foreach ($this->tableNames() as $table) {
            $quotedTable = $this->quoteIdentifier($table);
            $definition = DB::selectOne('SHOW CREATE TABLE ' . $quotedTable);
            $createSql = array_values((array) $definition)[1] ?? null;

            if (!$createSql) {
                continue;
            }

            $lines[] = 'DROP TABLE IF EXISTS ' . $quotedTable . ';';
            $lines[] = $createSql . ';';

            $columns = collect(DB::select('SHOW COLUMNS FROM ' . $quotedTable))
                ->map(fn ($column) => $column->Field)
                ->all();

            if ($columns) {
                $columnList = implode(', ', array_map(fn ($column) => $this->quoteIdentifier($column), $columns));
                foreach (DB::table($table)->get() as $row) {
                    $values = array_map(fn ($column) => $this->quoteSqlValue($pdo, $row->{$column}), $columns);
                    $lines[] = 'INSERT INTO ' . $quotedTable . ' (' . $columnList . ') VALUES (' . implode(', ', $values) . ');';
                }
            }

            $lines[] = '';
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS = 1;';

        return implode("\n", $lines) . "\n";
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function quoteSqlValue($pdo, mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        return $pdo->quote((string) $value);
    }
}
