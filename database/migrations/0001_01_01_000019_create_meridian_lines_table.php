<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 经脉线表（每条经脉在不同侧别的几何线）
     * 数据来源：renti/public/data/site-data.json -> lines
     */
    public function up(): void
    {
        Schema::create('meridian_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meridian_id')->comment('所属经脉ID（关联 meridians.id）');
            $table->string('meridian_code', 8)->comment('经脉代码冗余字段，便于查询');
            $table->string('side', 2)->comment('侧别：L左 / R右 / C中');
            $table->string('color', 16)->comment('颜色（十六进制）');
            $table->string('obj', 128)->comment('OBJ 模型文件相对路径');
            $table->decimal('pos_x', 10, 4)->comment('X 坐标');
            $table->decimal('pos_y', 10, 4)->comment('Y 坐标');
            $table->decimal('pos_z', 10, 4)->comment('Z 坐标');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('meridian_id');
            $table->index('meridian_code');
            $table->index('side');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meridian_lines');
    }
};
