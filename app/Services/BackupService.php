<?php

namespace App\Services;

use App\Models\DatabaseBackup;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BackupService
{
    /**
     * Membuat file dump database SQL dan mencatatnya ke database.
     */
    public function createBackup(?string $customFilename = null): DatabaseBackup
    {
        $filename = $customFilename ?? 'backup_' . config('app.name', 'absensi') . '_' . date('Y_m_d_His') . '.sql';
        $relativeDir = 'backups';
        $relativePath = $relativeDir . '/' . $filename;

        // Ensure storage directory exists
        if (!Storage::disk('local')->exists($relativeDir)) {
            Storage::disk('local')->makeDirectory($relativeDir);
        }

        // Get database tables
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::connection()->getDatabaseName();
        $keyName = 'Tables_in_' . $dbName;

        $sqlContent = "-- ABSENSI QR CODE DATABASE BACKUP DUMP\n";
        $sqlContent .= "-- Generated at: " . date('Y-m-d H:i:s') . "\n";
        $sqlContent .= "-- App Version: " . config('app.version', 'v1.0.0-PROD') . "\n";
        $sqlContent .= "-- Engine: Laravel 10 / MySQL\n\n";
        $sqlContent .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $tableObj) {
            $tableName = $tableObj->$keyName ?? current((array) $tableObj);
            
            // Structure
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            if (!empty($createTable)) {
                $createSql = $createTable[0]->{'Create Table'} ?? current((array)$createTable[0]);
                $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sqlContent .= $createSql . ";\n\n";
            }

            // Rows
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                foreach ($rows as $row) {
                    $rowArray = (array) $row;
                    $values = array_map(function ($value) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        return "'" . addslashes($value) . "'";
                    }, array_values($rowArray));

                    $sqlContent .= "INSERT INTO `{$tableName}` (`" . implode('`, `', array_keys($rowArray)) . "`) VALUES (" . implode(', ', $values) . ");\n";
                }
                $sqlContent .= "\n";
            }
        }

        $sqlContent .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Save to storage
        Storage::disk('local')->put($relativePath, $sqlContent);

        $fileSize = Storage::disk('local')->size($relativePath);
        $fullPath = Storage::disk('local')->path($relativePath);

        $currentUser = Auth::user();

        $backup = DatabaseBackup::create([
            'filename'   => $filename,
            'file_path'  => $fullPath,
            'file_size'  => $fileSize,
            'status'     => 'completed',
            'created_by' => $currentUser ? $currentUser->id : null,
        ]);

        ActivityLog::log(
            'Membuat Backup Database',
            'Backup Database',
            "Super Administrator membuat backup database baru ({$filename}, " . $backup->formatted_size . ").",
            $currentUser
        );

        return $backup;
    }

    /**
     * Ambil data backup terakhir.
     */
    public function getLastBackup(): ?DatabaseBackup
    {
        return DatabaseBackup::orderBy('created_at', 'desc')->first();
    }
}
