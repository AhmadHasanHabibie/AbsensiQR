<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentMailbox extends Model
{
    use HasFactory;

    protected $table = 'student_mailboxes';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'mail_type',
        'title',
        'description',
        'pdf_file',
        'week_start',
        'week_end',
        'alpha_total',
        'status',
    ];

    protected $casts = [
        'week_start' => 'date',
        'week_end'   => 'date',
    ];

    /**
     * Relasi ke siswa penerima surat.
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id', 'id');
    }

    /**
     * Relasi ke guru pengirim surat.
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }

    /**
     * Nama jenis surat.
     */
    public function getMailTypeNameAttribute(): string
    {
        return match ($this->mail_type) {
            'late'       => 'Pembinaan Keterlambatan',
            'permission' => 'Klarifikasi Izin',
            default      => 'Pemanggilan Orang Tua / Wali',
        };
    }

    /**
     * HTML Badge kategori jenis surat.
     */
    public function getMailTypeBadgeAttribute(): string
    {
        return match ($this->mail_type) {
            'late'       => '<span class="badge bg-warning text-dark"><i class="bi bi-circle-fill me-1 small"></i> 🟡 Pembinaan</span>',
            'permission' => '<span class="badge bg-info text-white"><i class="bi bi-circle-fill me-1 small"></i> 🔵 Klarifikasi</span>',
            default      => '<span class="badge bg-danger"><i class="bi bi-circle-fill me-1 small"></i> 🔴 Pemanggilan</span>',
        };
    }
}
