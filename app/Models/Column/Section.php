<?php

namespace App\Models\Column;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'content_column_sections';

    protected $fillable = [
        'column_id',
        'nameEn',
        'title',
        'subtitle',
        'h2',
        'span',
        'html',
        'css',
        'js',
        'cover',
        'scw',
        'stc',
        'sbi',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'column_id' => 'integer',
            'status' => 'integer',
            'sort' => 'integer',
        ];
    }

    /**
     * 关联栏目
     */
    public function column()
    {
        return $this->belongsTo(Column::class);
    }

    /**
     * 启用状态
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
