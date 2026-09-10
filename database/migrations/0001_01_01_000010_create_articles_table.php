<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('content_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('文章标题');
            $table->string('slug')->unique()->comment('文章别名');
            $table->text('content')->comment('文章内容');
            $table->string('excerpt')->nullable()->comment('文章摘要');
            $table->string('cover')->nullable()->comment('封面图');
            $table->string('keywords')->nullable()->comment('SEO关键词');
            $table->string('description')->nullable()->comment('SEO描述');
            $table->string('code')->nullable()->comment('自定义代码');
            $table->tinyInteger('flag_s')->default(0)->comment('推荐 1是 0否');
            $table->tinyInteger('flag_c')->default(0)->comment('热门 1是 0否');
            $table->tinyInteger('flag_o')->default(0)->comment('置顶 1是 0否');
            $table->unsignedInteger('like')->default(0)->comment('点赞数');
            $table->unsignedBigInteger('category_id')->default(0)->comment('分类ID');
            $table->unsignedBigInteger('author_id')->default(0)->comment('作者ID');
            $table->unsignedInteger('views')->default(0)->comment('浏览量');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1发布 0草稿');
            $table->timestamp('published_at')->nullable()->comment('发布时间');
            $table->softDeletes();
            $table->timestamps();

            $table->index('category_id');
            $table->index('author_id');
            $table->index('status');
            $table->index('sort');
            $table->index('published_at');
            $table->index('flag_s');
            $table->index('flag_c');
            $table->index('flag_o');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_articles');
    }
};
