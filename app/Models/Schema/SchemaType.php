<?php

namespace App\Models\Schema;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchemaType extends Model
{
	use SoftDeletes;
    protected $table = 'schema_types';

    protected $fillable = [
        'parent_id',
        'name',
        'slug',
        'is_leaf',
        'depth',
        'description',
        'example',
        'url',
        'sort',
        'status',
    ];

    protected $casts = [
        'is_leaf' => 'integer',
        'depth'   => 'integer',
        'status'  => 'integer',
    ];

    // 缓存有效期，单位：秒；0=永久缓存直到主动清除
    public const PROPERTY_CACHE_TTL = 0;

    //----------------关系----------------
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(SchemaProperty::class, 'type_id');
    }

    //----------------树工具方法----------------
    public function getAllChildren(): Collection
    {
        $children = collect();
        foreach ($this->children as $child) {
            $children->push($child);
            $children = $children->merge($child->getAllChildren());
        }
        return $children;
    }

    public function getAllParents(): Collection
    {
        $parents = collect();
        $current = $this->parent;
        while ($current) {
            $parents->push($current);
            $current = $current->parent;
        }
        return $parents;
    }

    public static function buildTree($parentId = null): array
    {
        $nodes = self::where('parent_id', $parentId)->orderBy('sort')->get();
        $tree = [];
        foreach ($nodes as $node) {
            $item = $node->toArray();
            $item['children'] = self::buildTree($node->id);
            $tree[] = $item;
        }
        return $tree;
    }

    //----------------缓存版 获取完整继承属性----------------
    /**
     * 获取该类型完整属性集合（自有属性 + 所有祖先自有属性）【带缓存】
     * @return Collection
     */
    public function getAllInheritedProperties(): Collection
    {
        $cacheKey = $this->getPropertyCacheKey();

        return Cache::remember($cacheKey, self::PROPERTY_CACHE_TTL, function () {
            $props = $this->properties;

            if ($this->parent) {
                // 递归获取父级（父级自身也会走缓存）
                $props = $props->merge($this->parent->getAllInheritedProperties());
            }

            return $props;
        });
    }

    /**
     * 清除当前类型属性缓存
     */
    public function flushPropertyCache(): void
    {
        Cache::forget($this->getPropertyCacheKey());
    }

    /**
     * 清除当前 + 所有后代子节点缓存
     * 修改父类型属性后，所有子类继承结果都要失效
     */
    public function flushSelfAndDescendantsPropertyCache(): void
    {
        $this->flushPropertyCache();

        $descendants = $this->getAllChildren();
        foreach ($descendants as $item) {
            $item->flushPropertyCache();
        }
    }

    /**
     * 生成缓存键名
     */
    protected function getPropertyCacheKey(): string
    {
        return sprintf('schema_type:props:%d', $this->id);
    }

    //----------------模型事件：自动清除缓存----------------
    protected static function booted(): void
    {
        static::saved(function ($model) {
            $model->flushSelfAndDescendantsPropertyCache();
        });

        static::deleted(function ($model) {
            $model->flushSelfAndDescendantsPropertyCache();
        });
    }
}