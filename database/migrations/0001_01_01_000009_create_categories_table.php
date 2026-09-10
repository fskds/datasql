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
        Schema::create('content_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('类别名称');
            $table->string('slug')->unique()->comment('类别别名');
            $table->string('description')->nullable()->comment('类别描述');
            $table->unsignedBigInteger('pid')->default(0)->comment('父级ID');
            $table->string('imageUrl')->nullable()->comment('类别图片');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('pid');
            $table->index('status');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_categories');
    }
};
