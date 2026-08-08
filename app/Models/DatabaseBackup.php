<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Model
{
    use HasFactory;

    protected $table = 'database_backups';

    protected $fillable = [
        'filename',
        'file_path',
        'file_size',
        'status',
        'created_by',
    ];

    /**
     * Relasi ke User pembuat backup.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Format bytes menjadi human-readable string.
     * Helper internal — gunakan accessor di bawah untuk akses dari luar.
     */
    protected static function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Accessor: $backup->formatted_size
     * Membaca ukuran dari kolom file_size di database.
     * Jika file_size = 0 atau null, coba baca langsung dari storage.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;

        // Jika DB kosong / 0, coba baca dari filesystem langsung
        if ($bytes <= 0) {
            $bytes = $this->getActualFileSizeBytes();
        }

        if ($bytes <= 0) {
            return 'Tidak tersedia';
        }

        return static::formatBytes($bytes);
    }

    /**
     * Accessor: $backup->file_size_formatted
     * ALIAS dari formatted_size — agar semua kode yang memanggil nama ini juga berfungsi.
     */
    public function getFileSizeFormattedAttribute(): string
    {
        return $this->getFormattedSizeAttribute();
    }

    /**
     * Cek apakah file backup benar-benar ada di storage.
     */
    public function fileExists(): bool
    {
        return Storage::disk('local')->exists('backups/' . $this->filename);
    }

    /**
     * Baca ukuran file asli dari storage (bytes).
     * Mengembalikan 0 jika file tidak ada.
     */
    public function getActualFileSizeBytes(): int
    {
        $path = 'backups/' . $this->filename;
        if (Storage::disk('local')->exists($path)) {
            return (int) Storage::disk('local')->size($path);
        }
        return 0;
    }

    /**
     * Accessor: $backup->actual_size_formatted
     * Selalu membaca dari filesystem, bukan dari DB.
     * Berguna untuk validasi konsistensi.
     */
    public function getActualSizeFormattedAttribute(): string
    {
        $bytes = $this->getActualFileSizeBytes();
        if ($bytes <= 0) {
            return 'File tidak tersedia';
        }
        return static::formatBytes($bytes);
    }
}
