<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 穴道表（669 个穴位标记，含几何与位置信息）
     * 数据来源：renti/public/data/site-data.json -> acupoints
     */
    public function up(): void
    {
        Schema::create('acupoints', function (Blueprint $table) {
            $table->id();
            $table->string('point_id', 32)->unique()->comment('原始穴道ID（如 BL_L_10）');
            $table->unsignedBigInteger('meridian_id')->comment('所属经脉ID（关联 meridians.id）');
            $table->string('meridian_code', 8)->comment('经脉代码冗余字段');
            $table->string('side', 2)->comment('侧别：L左 / R右 / C中');
            $table->unsignedInteger('seq')->comment('经脉内序号');
            $table->string('name', 32)->comment('穴名（如 天柱）');
            $table->string('meridian_cn', 32)->comment('经脉中文名冗余');
            $table->string('color', 16)->comment('颜色（十六进制）');
            $table->string('obj', 128)->comment('OBJ 模型文件相对路径');
            $table->decimal('scale_x', 8, 4)->default(1.0)->comment('X 缩放');
            $table->decimal('scale_y', 8, 4)->default(1.0)->comment('Y 缩放');
            $table->decimal('scale_z', 8, 4)->default(1.0)->comment('Z 缩放');
            $table->decimal('x', 10, 4)->comment('X 坐标');
            $table->decimal('y', 10, 4)->comment('Y 坐标');
            $table->decimal('z', 10, 4)->comment('Z 坐标');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('meridian_id');
            $table->index('meridian_code');
            $table->index('side');
            $table->index('name');
            $table->index('seq');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acupoints');
    }
};
