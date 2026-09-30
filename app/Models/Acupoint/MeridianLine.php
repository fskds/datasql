<?php

namespace App\Models\Acupoint;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeridianLine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meridian_id',
        'meridian_code',
        'side',
        'color',
        'obj',
        'pos_x',
        'pos_y',
        'pos_z',
        'sort',
        'status',
    ];

    protected $casts = [
        'pos_x' => 'float',
        'pos_y' => 'float',
        'pos_z' => 'float',
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
     * 按侧别筛选
     */
    public function scopeOfSide($query, string $side)
    {
        return $query->where('side', $side);
    }
}
