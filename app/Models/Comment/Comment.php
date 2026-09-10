<?php

namespace App\Models\Comment;

use App\Models\Article\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'content_comments';

    protected $fillable = [
        'article_id',
        'content',
        'author_name',
        'author_email',
        'parent_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'article_id' => 'integer',
            'parent_id' => 'integer',
            'status' => 'integer',
        ];
    }

    /**
     * 所属文章
     */
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * 父评论
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * 子评论
     */
    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}