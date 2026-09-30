<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 模型装配表（皮肤 + 骨骼部件，供前端 3D 模型组装）
     * 数据来源：renti/data-source/site-data.json -> model.skin / model.bones
     * 一条记录 = 一个部件，at_json 存储装配变换数组（position/quat/scale）
     */
    public function up(): void
    {
        Schema::create('model_parts', function (Blueprint $table) {
            $table->id();
            $table->string('part_type', 8)->comment('部件类型：skin 皮肤 / bones 骨骼');
            $table->string('name', 64)->comment('部件名（如 jss_Arm_L_Layer1）');
            $table->string('obj', 128)->comment('OBJ 模型文件相对路径');
            $table->string('layer', 16)->comment('层级：skin_f / inner');
            $table->text('at_json')->comment('装配变换数组 JSON：[{go,position,quat,scale}]');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->timestamps();

            $table->index('part_type');
            $table->index('status');
            $table->index('sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_parts');
    }
};