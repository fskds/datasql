<?php

namespace App\Models\Link;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'content_links';

    protected $fillable = [
        'name',
        'url',
        'logo',
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
