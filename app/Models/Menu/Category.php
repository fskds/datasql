<?php

namespace App\Models\menu;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'content_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'pId',
        'imageUrl',
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
     * 父级分类
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'pid');
    }

    /**
     * 子级分类
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'pid');
    }

    /**
     * 启用状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * 关联Banner
     */
    public function banners()
    {
        return $this->belongsToMany(Banner::class, 'content_banner_category', 'category_id', 'banner_id')
            ->withPivot('sort')
            ->orderBy('content_banner_category.sort');
    }
}
