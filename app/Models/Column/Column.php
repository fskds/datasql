<?php

namespace App\Models\Column;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Banner\Banner;

class Column extends Model
{
    use HasFactory;

    protected $table = 'content_columns';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'keywords',
        'nav_id',
        'cover',
        'sort',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'nav_id' => 'integer',
            'sort' => 'integer',
        ];
    }

    /**
     * 关联导航
     */
    public function nav()
    {
        return $this->belongsTo(Nav::class);
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
        return $this->belongsToMany(Banner::class, 'content_banner_column', 'column_id', 'banner_id');
    }

    /**
     * 关联区块
     */
    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('sort');
    }
}
