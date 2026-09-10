<?php

namespace App\Models\menu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
class Nav extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'content_navs';

    protected $fillable = [
        'name',
        'pId',
        'slug',
        'groupId',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'pid' => 'integer',
            'sort' => 'integer',
        ];
    }

    /**
     * 父级导航
     */
    public function parent()
    {
        return $this->belongsTo(Nav::class, 'pid');
    }

    /**
     * 子级导航
     */
    public function children(): HasMany
    {
        return $this->hasMany(Nav::class, 'pid');
    }

    /**
     * 启用状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
