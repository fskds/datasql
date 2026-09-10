<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Schema.org 类型表（参考 https://schema.org.cn/docs/full.html 的完整类型层次结构）
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schema_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父类型ID，0 表示顶层类型');
            $table->string('name')->comment('类型名称（英文，如 Thing / CreativeWork）');
            $table->string('slug')->unique()->comment('类型别名（kebab-case）');
            $table->tinyInteger('is_leaf')->default(0)->comment('是否叶子类型 1是 0否');
            $table->unsignedTinyInteger('depth')->default(0)->comment('层级深度，顶层为 0');
            $table->text('description')->nullable()->comment('类型中文描述');
            $table->string('url')->nullable()->comment('官方类型链接');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
			$table->softDeletes();
            $table->timestamps();

            $table->index('parent_id');
            $table->index('status');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schema_types');
    }
};