<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SiteInfo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'site_infos';

    protected $fillable = [
        'varname',
        'info',
        'groupid',
        'type',
        'value',
        'status',
    ];

    /**
     * 按分组获取配置
     */
    public function scopeByGroup($query, string $groupid)
    {
        return $query->where('groupid', $groupid);
    }

    /**
     * 根据变量名获取配置值
     */
    public static function getValue(string $varname, ?string $default = null): ?string
    {
        $config = self::where('varname', $varname)->first();
        return $config ? $config->value : $default;
    }

    /**
     * 获取分组下所有配置（键值对）
     */
    public static function getGroup(string $groupid): array
    {
        return self::byGroup($groupid)
            ->pluck('value', 'varname')
            ->toArray();
    }
}
