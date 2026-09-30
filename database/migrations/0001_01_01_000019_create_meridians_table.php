<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 经脉表（十四经脉：十二正经 + 任督二脉）
     * 数据来源：renti/public/data/site-data.json -> meridians
     */
    public function up(): void
    {
        Schema::create('meridians', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique()->comment('经脉代码（如 LU/LI/ST/CV/GV）');
            $table->string('cn', 32)->comment('中文名（如 手太阴肺经）');
            $table->string('pinyin', 64)->comment('拼音（如 shou tai yin fei jing）');
            $table->string('type', 8)->comment('类型：yin/yang/ren/du');
            $table->string('color', 16)->comment('颜色（十六进制）');
            $table->text('desc')->nullable()->comment('经脉描述');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index('sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meridians');
    }
};
