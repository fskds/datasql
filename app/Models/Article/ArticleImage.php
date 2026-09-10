<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleImage extends Model
{
    use HasFactory;

    protected $table = 'content_article_image';

    protected $fillable = [
        'article_id',
        'image_id',
        'sort',
    ];

    protected function casts(): array
    {
        return [
            'article_id' => 'integer',
            'image_id' => 'integer',
            'sort' => 'integer',
        ];
    }

    /**
     * 关联文章
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 关联图片
     */
    public function image()
    {
        return $this->belongsTo(Image::class);
    }
}
