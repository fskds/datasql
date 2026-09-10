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
        Schema::create('content_columns', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('栏目名称');
            $table->string('slug')->unique()->comment('栏目别名');
            $table->string('description')->nullable()->comment('栏目描述');
            $table->string('keywords')->nullable()->comment('SEO关键词');
            $table->unsignedBigInteger('nav_id')->default(0)->comment('导航ID');
            $table->string('cover')->nullable()->comment('栏目封面');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('nav_id');
            $table->index('status');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_columns');
    }
};
