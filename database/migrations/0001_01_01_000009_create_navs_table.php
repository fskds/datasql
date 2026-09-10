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
        Schema::create('content_navs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('导航名称');
            $table->string('slug')->default('')->comment('路径');
            $table->unsignedBigInteger('pId')->default(0)->comment('父级ID');
            $table->string('groupId')->comment('导航组');
            $table->unsignedInteger('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态 1启用 0禁用');
            $table->softDeletes();
            $table->timestamps();

            $table->index('pid');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_navs');
    }
};
