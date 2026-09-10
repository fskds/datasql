<?php

namespace App\Http\Controllers\Backend\System;

use App\Http\Controllers\Controller;
use App\Models\Backup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    private string $backupDisk = 'backup';
    private string $backupDir  = '';

    /**
     * 备份列表
     * GET /api-admin/system/backup
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $this->ensureTableExists();
            $query = Backup::query()->orderBy('created_at', 'desc');

            if ($request->has('type') && $request->type) {
                $query->ofType($request->type);
            }

            $list = $query->paginate(10);

            return response()->json([
                'status' => true,
                'msg'    => '获取成功',
                'data'   => $list->items(),
                'total'  => $list->total(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => true,
                'msg'    => '获取成功',
                'data'   => [],
                'total'  => 0,
            ]);
        }
    }

    /**
     * 创建备份
     * POST /api-admin/system/backup/store
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'type'        => 'required|in:database,files',
            'description' => 'required|string|max:200',
        ]);

        $type = $request->input('type');
        $description = $request->input('description');
        $timestamp = date('Ymd_His');
        $status = 'success';

        try {
            $this->ensureTableExists();

            if ($type === 'database') {
                $result = $this->backupDatabase($timestamp);
            } else {
                $result = $this->backupFiles($timestamp);
            }

            $backup = Backup::create([
                'type'        => $type,
                'filename'    => $result['filename'],
                'filepath'    => $result['filepath'],
                'filesize'    => $result['filesize'],
                'description' => $description,
                'status'      => $status,
            ]);

            return response()->json([
                'status' => true,
                'msg'    => '备份创建成功',
                'data'   => $backup,
            ]);

        } catch (\Throwable $e) {
            // 尝试记录失败记录（表不存在时跳过）
            try {
                Backup::create([
                    'type'        => $type,
                    'filename'    => "{$type}_{$timestamp}",
                    'filepath'    => '',
                    'filesize'    => '0 B',
                    'description' => $description,
                    'status'      => 'failed',
                ]);
            } catch (\Throwable $ignore) {
                // 表不存在，忽略
            }

            return response()->json([
                'status' => false,
                'msg'    => '备份创建失败: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 下载备份文件
     * GET /api-admin/system/backup/download/{id}
     */
    public function download($id): mixed
    {
        $backup = Backup::findOrFail($id);
        $filepath = $this->getDiskPath($backup);

        if (!Storage::disk($this->backupDisk)->exists($filepath)) {
            return response()->json([
                'status' => false,
                'msg'    => '备份文件不存在',
            ], 404);
        }

        return Storage::disk($this->backupDisk)->download(
            $filepath,
            $backup->filename
        );
    }

    /**
     * 获取文件在磁盘中的实际路径（兼容旧记录中 backup/ 前缀）
     */
    private function getDiskPath(Backup $backup): string
    {
        $path = $backup->filepath;
        // 兼容旧记录：去掉 backup/ 前缀
        if (str_starts_with($path, 'backup/')) {
            $path = substr($path, 7);
        }
        return $path;
    }

    /**
     * 删除备份
     * DELETE /api-admin/system/backup/delete/{id}
     */
    public function destroy($id): JsonResponse
    {
        $backup = Backup::findOrFail($id);

        // 删除物理文件
        $filepath = $this->getDiskPath($backup);
        if (Storage::disk($this->backupDisk)->exists($filepath)) {
            Storage::disk($this->backupDisk)->delete($filepath);
        }

        $backup->delete();

        return response()->json([
            'status' => true,
            'msg'    => '备份已删除',
        ]);
    }

    /**
     * 还原备份
     * POST /api-admin/system/backup/restore/{id}
     */
    public function restore($id): JsonResponse
    {
        $backup = Backup::findOrFail($id);
        $filepath = $this->getDiskPath($backup);

        if (!Storage::disk($this->backupDisk)->exists($filepath)) {
            return response()->json([
                'status' => false,
                'msg'    => '备份文件不存在，无法还原',
            ], 404);
        }

        try {
            if ($backup->type === 'database') {
                $this->restoreDatabase($filepath);
            } else {
                $this->restoreFiles($filepath);
            }

            return response()->json([
                'status' => true,
                'msg'    => '备份还原成功',
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => false,
                'msg'    => '还原失败: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 获取定时备份配置
     * GET /api-admin/system/backup/schedule
     */
    public function getSchedule(): JsonResponse
    {
        $config = $this->loadScheduleConfig();

        return response()->json([
            'status' => true,
            'msg'    => '获取成功',
            'data'   => $config,
        ]);
    }

    /**
     * 保存定时备份配置
     * PUT /api-admin/system/backup/schedule
     */
    public function saveSchedule(Request $request): JsonResponse
    {
        $config = $this->loadScheduleConfig();

        $data = $request->only(['enabled', 'type', 'keep_count', 'cron']);
        $config = array_merge($config, $data);

        Storage::disk($this->backupDisk)->put(
            'schedule.json',
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        return response()->json([
            'status' => true,
            'msg'    => '定时备份配置已保存',
            'data'   => $config,
        ]);
    }

    // ==================== 私有方法 ====================

    /**
     * 确保 system_backups 表存在，不存在则自动创建
     */
    private function ensureTableExists(): void
    {
        if (!Schema::hasTable('system_backups')) {
            Schema::create('system_backups', function ($table) {
                $table->id();
                $table->string('type')->comment('备份类型');
                $table->string('filename')->comment('备份文件名');
                $table->string('filepath')->comment('备份文件路径');
                $table->string('filesize')->nullable()->comment('文件大小');
                $table->string('description')->nullable()->comment('备份说明');
                $table->string('status')->default('success')->comment('状态');
                $table->timestamps();
                $table->index('type');
                $table->index('status');
            });
        }
    }

    /**
     * 数据库备份（纯 PHP 实现，不依赖 mysqldump）
     */
    private function backupDatabase(string $timestamp): array
    {
        $filename = "database_{$timestamp}.sql";
        $filepath = $filename;

        $fullPath = storage_path('app/backup/' . $filepath);
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $handle = fopen($fullPath, 'w');

        if (!$handle) {
            throw new \RuntimeException('无法创建备份文件');
        }

        try {
            // 写入文件头
            fwrite($handle, "-- Database Backup\n");
            fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
            fwrite($handle, "-- Server: " . config('database.connections.' . config('database.default') . '.host') . "\n");
            fwrite($handle, "-- Database: " . DB::getDatabaseName() . "\n\n");
            fwrite($handle, "SET FOREIGN_KEY_CHECKS=0;\n\n");

            // 获取所有表
            $tables = DB::select('SHOW TABLES');
            $dbName = DB::getDatabaseName();
            $tableKey = 'Tables_in_' . $dbName;

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                // 跳过系统表
                if (in_array($tableName, ['migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'])) {
                    continue;
                }

                // 写入 DROP TABLE + CREATE TABLE
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createSql = $createTable[0]->{'Create Table'};

                fwrite($handle, "--\n-- Table: {$tableName}\n--\n\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                fwrite($handle, "{$createSql};\n\n");

                // 分批导出数据
                $total = DB::table($tableName)->count();
                if ($total === 0) {
                    continue;
                }

                fwrite($handle, "-- Data for table: {$tableName}\n\n");

                $chunkSize = 500;
                DB::table($tableName)->orderBy(DB::raw('1'))->chunk($chunkSize, function ($rows) use ($handle, $tableName) {
                    // 检查第一个非空行的列名，动态构建字段列表
                    if ($rows->isEmpty()) {
                        return;
                    }

                    $firstRow = (array) $rows->first();
                    $columns = array_keys($firstRow);
                    $colList = '`' . implode('`, `', $columns) . '`';

                    $values = [];
                    foreach ($rows as $row) {
                        $rowArr = (array) $row;
                        $vals = [];
                        foreach ($rowArr as $val) {
                            if (is_null($val)) {
                                $vals[] = 'NULL';
                            } else {
                                $vals[] = "'" . str_replace(
                                    ["\\", "'"],
                                    ["\\\\", "\\'"],
                                    (string) $val
                                ) . "'";
                            }
                        }
                        $values[] = '(' . implode(', ', $vals) . ')';
                    }

                    $sql = "INSERT INTO `{$tableName}` ({$colList}) VALUES\n" . implode(",\n", $values) . ";\n";
                    fwrite($handle, $sql);
                });

                fwrite($handle, "\n");
            }

            fwrite($handle, "SET FOREIGN_KEY_CHECKS=1;\n");

        } finally {
            fclose($handle);
        }

        $filesize = $this->formatBytes(filesize($fullPath));

        return compact('filename', 'filepath', 'filesize');
    }

    /**
     * 文件备份
     */
    private function backupFiles(string $timestamp): array
    {
        $filename = "files_{$timestamp}.zip";
        $filepath = $filename;

        $zipPath = storage_path('app/backup/' . $filepath);
        $zipDir = dirname($zipPath);
        if (!is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }
        $zip = new \ZipArchive();

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('无法创建 ZIP 文件');
        }

        // 备份 storage/app/public 目录
        $publicPath = storage_path('app/public');
        if (is_dir($publicPath)) {
            $this->addDirToZip($zip, $publicPath, 'public');
        }

        // 备份 .env 文件
        $envPath = base_path('.env');
        if (file_exists($envPath)) {
            $zip->addFile($envPath, '.env');
        }

        $zip->close();

        $filesize = $this->formatBytes(filesize($zipPath));

        return compact('filename', 'filepath', 'filesize');
    }

    /**
     * 递归添加目录到 ZIP
     */
    private function addDirToZip(\ZipArchive $zip, string $dir, string $relativePath): void
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            $zipPath = $relativePath . '/' . str_replace('\\', '/', $files->getSubPathname());

            if ($file->isDir()) {
                $zip->addEmptyDir($zipPath);
            } else {
                $zip->addFile($filePath, $zipPath);
            }
        }
    }

    /**
     * 数据库还原（逐条执行 SQL）
     */
    private function restoreDatabase(string $filepath): void
    {
        $fullPath = storage_path('app/backup/' . $filepath);

        if (!file_exists($fullPath)) {
            throw new \RuntimeException('备份文件不存在');
        }

        $sql = file_get_contents($fullPath);
        if (!$sql) {
            throw new \RuntimeException('备份文件为空');
        }

        // 按分号分割 SQL 语句，跳过注释和空行
        $statements = preg_split('/;\s*\n/', $sql);
        $current = '';

        foreach ($statements as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '--')) {
                continue;
            }
            $current .= $line;

            // 检查是否是完整语句（不以 -- 开头且不为空）
            if ($current !== '') {
                try {
                    DB::unprepared($current . ';');
                } catch (\Throwable $e) {
                    // 忽略单条语句错误，继续执行
                }
                $current = '';
            }
        }
    }

    /**
     * 文件还原
     */
    private function restoreFiles(string $filepath): void
    {
        $fullPath = storage_path('app/backup/' . $filepath);

        if (!file_exists($fullPath)) {
            throw new \RuntimeException('备份文件不存在');
        }

        $zip = new \ZipArchive();
        if ($zip->open($fullPath) !== true) {
            throw new \RuntimeException('无法打开备份文件');
        }

        // 还原到 storage/app/public
        $publicPath = storage_path('app/public');
        $zip->extractTo(dirname($publicPath));
        $zip->close();
    }

    /**
     * 加载定时备份配置
     */
    private function loadScheduleConfig(): array
    {
        $defaults = [
            'enabled'    => false,
            'type'       => 'database',
            'cron'       => '0 2 * * *',
            'keep_count' => 7,
            'last_run'   => null,
            'next_run'   => null,
        ];

        $configPath = 'schedule.json';

        if (Storage::disk($this->backupDisk)->exists($configPath)) {
            $saved = json_decode(
                Storage::disk($this->backupDisk)->get($configPath),
                true
            );
            if (is_array($saved)) {
                $defaults = array_merge($defaults, $saved);
            }
        }

        return $defaults;
    }

    /**
     * 格式化文件大小
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = floor(log($bytes, 1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
    }
}