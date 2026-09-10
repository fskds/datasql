<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'site_info_options';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'setting_id',
        'label',
        'value',
        'sort',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'setting_id' => 'integer',
        'sort'       => 'integer',
        'status'     => 'integer',
    ];

    /**
     * 获取所属的设置项
     */
    public function setting()
    {
        return $this->belongsTo(Setting::class, 'setting_id');
    }

    /**
     * 作用域：按 setting_id 查询
     */
    public function scopeOfSetting($query, int $settingId)
    {
        return $query->where('setting_id', $settingId);
    }

    /**
     * 作用域：只查询启用的选项
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * 作用域：按排序字段排序
     */
    public function scopeSorted($query)
    {
        return $query->orderBy('sort', 'asc')->orderBy('id', 'asc');
    }
}