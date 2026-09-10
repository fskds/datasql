<?php

namespace App\Models\menu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'content_tags';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'sort' => 'integer',
        ];
    }

    /**
     * 启用状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
