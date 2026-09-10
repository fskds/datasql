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
        Schema::create('attachment_images', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('图片标题');
            $table->string('url')->comment('图片地址');
            $table->string('thumb')->nullable()->comment('缩略图地址');
            $table->string('alt')->nullable()->comment('图片描述');
            $table->string('groupid')->default('default')->comment('分组ID');
            $table->unsignedInteger('size')->comment('图片大小');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('groupid');
            $table->index('status');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment_images');
    }
};
