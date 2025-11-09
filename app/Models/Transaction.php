<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','date','title','type','amount','cover_path','description',
    ];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'float',
    ];

    /** Simpan path bersih (tanpa public/|storage/, tanpa backslash) */
    public function setCoverPathAttribute($value): void
    {
        if (empty($value)) { $this->attributes['cover_path'] = null; return; }
        $p = ltrim(str_replace('\\','/',$value), '/');
        $p = preg_replace('#^(public/|storage/)#','', $p);
        $this->attributes['cover_path'] = $p; // contoh: covers/foo.jpg
    }

    /**
     * Helper untuk Blade:
     * - cek file di storage/app/public/{path}
     * - URL pakai route('media', ...) agar tidak bergantung symlink /storage
     *   dan otomatis cocok dengan host+port aktif.
     */
    public function coverFile(): array
    {
        if (!$this->cover_path) return [false, null, null];

        $p = ltrim(str_replace('\\','/',$this->cover_path), '/');
        $p = preg_replace('#^(public/|storage/)#','', $p);

        $full   = storage_path('app/public/'.$p);
        $exists = is_file($full);
        $url    = $exists ? route('media', ['path' => $p]) : null; // <-- /media/covers/xxx.jpg

        return [$exists, $url, $p]; // [bool exists, string|null url, string relativePath]
    }
}
