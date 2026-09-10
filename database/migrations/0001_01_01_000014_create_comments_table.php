<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id')->comment('文章ID');
            $table->text('content')->comment('评论内容');
			$table->string('author_id')->nullable()->comment('评论者id');
            $table->string('author_name')->nullable()->comment('评论者名称');
            $table->string('author_email')->nullable()->comment('评论者邮箱');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父评论ID');
            $table->tinyInteger('status')->default(1)->comment('状态 1显示 0隐藏');
            $table->softDeletes();
            $table->timestamps();

            $table->index('article_id');
            $table->index('parent_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_comments');
    }
};