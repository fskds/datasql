<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Backup extends Model
{
    use HasFactory;

    protected $table = 'system_backups';

    protected $fillable = [
        'type',
        'filename',
        'filepath',
        'filesize',
        'description',
        'status',
    ];

    /**
     * 按类型筛选
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}