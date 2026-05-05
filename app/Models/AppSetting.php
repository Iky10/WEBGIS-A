<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App Setting: key-value store untuk konfigurasi global.
 *
 * Contoh key yang dikenal:
 *  - semester_aktif         → 'ganjil' | 'genap'
 *  - tahun_ajaran_aktif     → '2025/2026'
 */
class AppSetting extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'app_settings';

    public $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'key'   => 'string',
        'value' => 'string',
    ];

    public static $rules = [
        'key'   => 'required|string|max:255',
        'value' => 'nullable|string',
    ];

    /**
     * Helper: ambil value dari key, fallback ke $default.
     * Contoh: AppSetting::get('semester_aktif', 'ganjil')
     */
    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        return $row ? $row->value : $default;
    }

    /**
     * Helper: set/update value untuk key (upsert).
     */
    public static function set(string $key, $value): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
