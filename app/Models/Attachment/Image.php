<?php

namespace App\Models\Attachment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'attachment_images';

    protected $fillable = [
        'name',
        'imageUrl',
        'thumb',
        'alt',
        'groupid',
        'size',
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
     * 按分组获取
     */
    public function scopeByGroup($query, string $groupid)
    {
        return $query->where('groupid', $groupid);
    }

    /**
     * 关联文章
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'content_article_image', 'image_id', 'article_id')
            ->withPivot('sort')
            ->orderBy('content_article_image.sort');
    }
}
