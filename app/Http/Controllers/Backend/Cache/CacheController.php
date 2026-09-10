<?php

namespace App\Http\Controllers\Backend\Cache;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheController extends Controller
{
    /**
     * 清除所有缓存
     * POST /api-admin/system/cache/clear
     */
    public function clear(Request $request): JsonResponse
    {
        $results = [];

        try {
            // 1. 应用缓存
            Cache::flush();
            $results[] = ['name' => '应用缓存', 'status' => 'success'];

            // 2. 视图缓存
            Artisan::call('view:clear');
            $results[] = ['name' => '视图缓存', 'status' => 'success'];

            // 3. 编译缓存
            Artisan::call('clear-compiled');
            $results[] = ['name' => '编译缓存', 'status' => 'success'];

            // 4. 事件缓存
            Artisan::call('event:clear');
            $results[] = ['name' => '事件缓存', 'status' => 'success'];

            // 5. 配置缓存
            Artisan::call('config:clear');
            $results[] = ['name' => '配置缓存', 'status' => 'success'];

            // 6. 路由缓存
            Artisan::call('route:clear');
            $results[] = ['name' => '路由缓存', 'status' => 'success'];

            // 7. 数据库缓存表
            if (DB::getSchemaBuilder()->hasTable('cache')) {
                DB::table('cache')->truncate();
                $results[] = ['name' => '数据库缓存表', 'status' => 'success'];
            }

            // 8. 数据库锁表
            if (DB::getSchemaBuilder()->hasTable('cache_locks')) {
                DB::table('cache_locks')->truncate();
                $results[] = ['name' => '数据库锁表', 'status' => 'success'];
            }

            return response()->json([
                'status'  => true,
                'msg'     => '所有缓存清除成功',
                'data'    => $results,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status'  => false,
                'msg'     => '缓存清除失败: ' . $e->getMessage(),
                'data'    => $results,
            ], 500);
        }
    }

    /**
     * 清除指定类型缓存
     * POST /api-admin/system/cache/clear/{type}
     */
    public function clearByType(string $type): JsonResponse
    {
        try {
            switch ($type) {
                case 'app':
                    Cache::flush();
                    break;
                case 'view':
                    Artisan::call('view:clear');
                    break;
                case 'config':
                    Artisan::call('config:clear');
                    break;
                case 'route':
                    Artisan::call('route:clear');
                    break;
                case 'event':
                    Artisan::call('event:clear');
                    break;
                case 'compiled':
                    Artisan::call('clear-compiled');
                    break;
                case 'database_cache':
                    if (DB::getSchemaBuilder()->hasTable('cache')) {
                        DB::table('cache')->truncate();
                    }
                    break;
                case 'database_locks':
                    if (DB::getSchemaBuilder()->hasTable('cache_locks')) {
                        DB::table('cache_locks')->truncate();
                    }
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'msg'    => '未知缓存类型: ' . $type,
                    ], 400);
            }

            // 缓存类型中文映射
            $labels = [
                'app'            => '应用缓存',
                'view'           => '视图缓存',
                'config'         => '配置缓存',
                'route'          => '路由缓存',
                'event'          => '事件缓存',
                'compiled'       => '编译缓存',
                'database_cache' => '数据库缓存表',
                'database_locks' => '数据库锁表',
            ];

            return response()->json([
                'status' => true,
                'msg'    => ($labels[$type] ?? $type) . ' 清除成功',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'msg'    => '清除失败: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 获取缓存状态
     * GET /api-admin/system/cache/status
     */
    public function status(): JsonResponse
    {
        $cacheDriver = config('cache.default');
        $items = [];

        // 缓存类型定义
        $types = [
            ['key' => 'app',            'name' => '应用缓存',     'desc' => 'Laravel Cache::flush()，清除所有应用级缓存数据'],
            ['key' => 'view',           'name' => '视图缓存',     'desc' => 'php artisan view:clear，清除已编译的 Blade 模板'],
            ['key' => 'compiled',       'name' => '编译缓存',     'desc' => 'php artisan clear-compiled，清除框架编译后的类文件'],
            ['key' => 'event',          'name' => '事件缓存',     'desc' => 'php artisan event:clear，清除已缓存的事件监听器'],
            ['key' => 'config',         'name' => '配置缓存',     'desc' => 'php artisan config:clear，清除已缓存的配置信息'],
            ['key' => 'route',          'name' => '路由缓存',     'desc' => 'php artisan route:clear，清除已缓存的路由信息'],
            ['key' => 'database_cache', 'name' => '数据库缓存表', 'desc' => '清空 cache 数据表，清除数据库驱动缓存'],
            ['key' => 'database_locks', 'name' => '数据库锁表',   'desc' => '清空 cache_locks 数据表，清除数据库原子锁'],
        ];

        foreach ($types as $type) {
            $items[] = [
                'key'  => $type['key'],
                'name' => $type['name'],
                'desc' => $type['desc'],
            ];
        }

        return response()->json([
            'status' => true,
            'msg'    => '获取成功',
            'data'   => [
                'driver' => $cacheDriver,
                'items'  => $items,
            ],
        ]);
    }
}