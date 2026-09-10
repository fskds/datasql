<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Schema.org 属性表（参考 https://schema.org.cn/docs/full.html 的类型属性信息）
     * 每行记录某类型下定义的一个属性及其期望取值类型。
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schema_properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id')->comment('所属类型ID（关联 schema_types.id）');
            $table->string('name')->comment('属性名称（英文，如 about / address）');
            $table->string('slug')->nullable()->comment('属性别名（snake-case）');
            $table->string('expected_type')->nullable()->comment('期望取值类型，如 Text / Person / Thing');
            $table->text('description')->nullable()->comment('属性中文描述');
            $table->string('url')->nullable()->comment('官方属性链接');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
			$table->softDeletes();
            $table->timestamps();

            $table->index('type_id');
            $table->index('status');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schema_properties');
    }
};