<?php

namespace App\Models\Article;

use App\Models\menu\Category;
use App\Models\Admin\Admin;
use App\Models\Attachment\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'content_articles';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'cover',
        'keywords',
        'description',
        'code',
        'flag_s',
        'flag_c',
        'flag_o',
        'like',
        'category_id',
        'author_id',
        'views',
        'sort',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'category_id' => 'integer',
            'author_id' => 'integer',
            'views' => 'integer',
            'sort' => 'integer',
            'like' => 'integer',
            'flag_s' => 'integer',
            'flag_c' => 'integer',
            'flag_o' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    /**
     * 已发布
     */
    public function scopePublished($query)
    {
        return $query->where('status', 1);
    }

    /**
     * 推荐
     */
    public function scopeRecommend($query)
    {
        return $query->where('flag_s', 1);
    }

    /**
     * 热门
     */
    public function scopeHot($query)
    {
        return $query->where('flag_c', 1);
    }

    /**
     * 置顶
     */
    public function scopeTop($query)
    {
        return $query->where('flag_o', 1);
    }

    /**
     * 分类
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 作者
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    /**
     * 关联图片
     */
    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, 'content_article_image', 'article_id', 'image_id')
            ->withPivot('sort')
            ->orderBy('content_article_image.sort');
    }
}