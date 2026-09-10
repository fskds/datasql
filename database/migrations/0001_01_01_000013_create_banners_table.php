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
        Schema::create('content_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable()->comment('标题');
            $table->string('link')->nullable()->comment('链接地址');
            $table->text('html')->nullable()->comment('自定义HTML');
            $table->text('css')->nullable()->comment('自定义CSS');
            $table->string('cover')->nullable()->comment('封面图');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('status');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_banners');
    }
};
