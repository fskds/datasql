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
        Schema::create('site_infos', function (Blueprint $table) {
            $table->id();
            $table->string('varname')->comment('变量名');
            $table->text('info')->nullable()->comment('配置说明');
            $table->string('groupid')->default('base')->comment('分组ID');
            $table->string('type')->default('text')->comment('类型：text,textarea,image,file,select等');
            
            $table->text('value')->nullable()->comment('配置值');
            $table->softDeletes();
            $table->timestamps();

            $table->unique('varname');
            $table->index('groupid');
        });

        Schema::create('site_info_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('setting_id')->comment('所属配置ID');
            $table->string('label', 100)->comment('选项标签');
            $table->string('value', 255)->comment('选项值');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->timestamps();

            $table->index('setting_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_infos');
        Schema::dropIfExists('site_info_options');
    }
};
