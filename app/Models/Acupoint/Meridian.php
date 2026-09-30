<?php

namespace App\Models\Acupoint;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meridian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'cn',
        'pinyin',
        'type',
        'color',
        'desc',
        'sort',
        'status',
    ];

    protected $casts = [
        'sort' => 'integer',
        'status' => 'integer',
    ];

    /**
     * 经脉线（一对多）
     */
    public function lines(): HasMany
    {
        return $this->hasMany(MeridianLine::class, 'meridian_id');
    }

    /**
     * 穴道（一对多）
     */
    public function acupoints(): HasMany
    {
        return $this->hasMany(Acupoint::class, 'meridian_id');
    }

    /**
     * 穴位介绍（一对多）
     */
    public function descs(): HasMany
    {
        return $this->hasMany(AcupointDesc::class, 'meridian_code', 'code');
    }

    /**
     * 按状态筛选
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * 按类型筛选
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
