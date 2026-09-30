<?php

namespace App\Models\Acupoint;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Acupoint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'point_id',
        'meridian_id',
        'meridian_code',
        'side',
        'seq',
        'name',
        'meridian_cn',
        'color',
        'obj',
        'scale_x',
        'scale_y',
        'scale_z',
        'x',
        'y',
        'z',
        'sort',
        'status',
    ];

    protected $casts = [
        'seq' => 'integer',
        'scale_x' => 'float',
        'scale_y' => 'float',
        'scale_z' => 'float',
        'x' => 'float',
        'y' => 'float',
        'z' => 'float',
        'sort' => 'integer',
        'status' => 'integer',
    ];

    /**
     * 所属经脉（多对一）
     */
    public function meridian(): BelongsTo
    {
        return $this->belongsTo(Meridian::class, 'meridian_id');
    }

    /**
     * 介绍文案（多对一，按经脉代码 + 穴名关联）
     */
    public function desc(): BelongsTo
    {
        return $this->belongsTo(AcupointDesc::class, 'id', 'acupoint_id');
    }

    /**
     * 按侧别筛选
     */
    public function scopeOfSide($query, string $side)
    {
        return $query->where('side', $side);
    }
}
