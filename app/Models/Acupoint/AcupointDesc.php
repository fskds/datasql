<?php

namespace App\Models\Acupoint;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcupointDesc extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'meridian_code',
        'name',
        'description',
        'acupoint_id',
        'sort',
        'status',
    ];

    protected $casts = [
        'sort' => 'integer',
        'status' => 'integer',
    ];

    /**
     * 关联穴道（多对一）
     */
    public function acupoint(): BelongsTo
    {
        return $this->belongsTo(Acupoint::class, 'acupoint_id');
    }

    /**
     * 关联经脉（按经脉代码）
     */
    public function meridian(): BelongsTo
    {
        return $this->belongsTo(Meridian::class, 'meridian_code', 'code');
    }
}
