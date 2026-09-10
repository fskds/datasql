<?php

namespace App\Console\Commands;

use App\Models\Schema\SchemaType;
use Illuminate\Console\Command;

/**
 * 从 schema.org.cn 抓取各类型的官方中文描述，覆盖 schema_types.description。
 * 类型页结构：面包屑位于 <h1 class="page-title"> 内，其后紧跟描述文本，再到属性表格 <table>。
 */
class SchemaFetchDescriptions extends Command
{
    protected $signature = 'schema:fetch-descriptions {--skip-empty : 仅覆盖 description 为空的类型}';

    protected $description = '从 schema.org.cn 抓取官方中文描述并覆盖 schema_types.description';

    public function handle(): int
    {
        $query = SchemaType::query();
        if ($this->option('skip-empty')) {
            $query->where('description', '');
        }
        $types = $query->orderBy('id')->get();

        $this->info("待处理类型：{$types->count()} 个");

        $updated = 0;
        $noDesc = 0;
        $failed = 0;
        $failedNames = [];

        foreach ($types as $type) {
            $name = $type->name;
            $url = 'https://schema.org.cn/' . rawurlencode($name) . '.html';
            $html = $this->fetch($url);

            if ($html === null) {
                $failed++;
                $failedNames[] = $name;
                $this->warn("  [失败] {$name}: 请求失败");
                continue;
            }

            $desc = $this->extract($html);

            if ($desc === '') {
                $noDesc++;
                $this->warn("  [无描述] {$name}");
            }

            if ($type->description !== $desc) {
                $type->description = $desc;
                $type->save();
            }
            $updated++;

            // 轻微延迟，避免对目标站点造成压力
            usleep(100000);
        }

        $this->info("完成：处理 {$updated}，其中无官方描述 {$noDesc}，请求失败 {$failed}");
        if ($failedNames) {
            $this->line('失败列表：' . implode(', ', $failedNames));
        }

        return self::SUCCESS;
    }

    private function fetch(string $url): ?string
    {
        $ctx = stream_context_create([
            'http' => [
                'timeout'       => 20,
                'ignore_errors' => true,
                'header'        => "User-Agent: Mozilla/5.0 (compatible; SchemaSync/1.0)\r\n",
            ],
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
            ],
        ]);

        $html = @file_get_contents($url, false, $ctx);

        return $html === false ? null : $html;
    }

    private function extract(string $html): string
    {
        // 优先：面包屑 h1 之后 -> 属性表格 <table> 之前的描述文本
        if (preg_match('~<h1 class="page-title">.*?</h1>\s*(.*?)(?=<table)~is', $html, $m)) {
            return $this->clean($m[1]);
        }
        // 无属性表格的类型页：h1 之后、下一个二级标题/块之前的描述文本
        if (preg_match('~<h1 class="page-title">.*?</h1>\s*(.*?)(?=</div>|<h2|更多具体类型)~is', $html, $m)) {
            return $this->clean($m[1]);
        }

        return '';
    }

    private function clean(string $fragment): string
    {
        $text = trim(strip_tags($fragment));

        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}