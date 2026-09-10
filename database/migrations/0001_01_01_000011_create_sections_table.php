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
        Schema::create('content_column_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('column_id')->comment('栏目ID');
            $table->string('nameEn')->nullable()->comment('英文名称');
            $table->string('title')->nullable()->comment('区块标题');
            $table->string('subtitle')->nullable()->comment('区块副标题');
            $table->string('h2')->nullable()->comment('H2标题');
            $table->string('span')->nullable()->comment('Span描述');
            $table->text('html')->nullable()->comment('自定义HTML');
            $table->text('css')->nullable()->comment('自定义CSS');
            $table->text('js')->nullable()->comment('自定义JS');
            $table->string('cover')->nullable()->comment('封面图');
            $table->string('scw')->nullable()->comment('展示宽度');
            $table->string('stc')->nullable()->comment('展示颜色');
            $table->string('sbi')->nullable()->comment('展示背景图');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('column_id');
            $table->index('status');
            $table->index('sort');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_column_sections');
    }
};
