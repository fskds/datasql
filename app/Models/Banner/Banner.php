<?php

namespace App\Models\Banner;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'content_banners';

    protected $fillable = [
        'title',
        'link',
        'html',
        'css',
        'cover',
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

    /**
     * 关联分类
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'content_banner_category', 'banner_id', 'category_id')
            ->withPivot('sort')
            ->orderBy('content_banner_category.sort');
    }

    /**
     * 关联栏目
     */
    public function columns()
    {
        return $this->belongsToMany(Column::class, 'content_banner_column', 'banner_id', 'column_id')
            ->withPivot('sort')
            ->orderBy('content_banner_column.sort');
    }
}
