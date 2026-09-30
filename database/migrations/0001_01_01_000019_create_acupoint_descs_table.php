<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 穴位介绍表（定位 + 主治 文案）
     * 数据来源：renti/public/data/acupoint_descs.json（键：MERIDIAN:穴名）
     */
    public function up(): void
    {
        Schema::create('acupoint_descs', function (Blueprint $table) {
            $table->id();
            $table->string('meridian_code', 8)->comment('经脉代码');
            $table->string('name', 32)->comment('穴名');
            $table->text('description')->comment('定位与主治文案');
            $table->unsignedBigInteger('acupoint_id')->nullable()->comment('关联穴道ID（acupoints.id，可空）');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('meridian_code');
            $table->index('name');
            $table->index('acupoint_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acupoint_descs');
    }
};
