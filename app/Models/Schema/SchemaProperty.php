<?php

namespace App\Models\Schema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchemaProperty extends Model
{
	use SoftDeletes;
    protected $table = 'schema_properties';

    protected $fillable = [
        'type_id',
        'name',
        'slug',
        'expected_type',
        'description',
        'url',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    public function schemaType(): BelongsTo
    {
        return $this->belongsTo(SchemaType::class, 'type_id');
    }

    protected static function booted(): void
    {
        // 属性新增/修改/删除 → 清除该类型以及所有子类缓存
        static::saved(function ($model) {
            $model->schemaType?->flushSelfAndDescendantsPropertyCache();
        });

        static::deleted(function ($model) {
            $model->schemaType?->flushSelfAndDescendantsPropertyCache();
        });
    }
}