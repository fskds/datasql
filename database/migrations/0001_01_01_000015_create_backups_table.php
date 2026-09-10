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
        Schema::create('system_backups', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('备份类型：database 数据库备份，files 文件备份');
            $table->string('filename')->comment('备份文件名');
            $table->string('filepath')->comment('备份文件路径');
            $table->string('filesize')->nullable()->comment('文件大小（可读格式）');
            $table->string('description')->nullable()->comment('备份说明');
            $table->string('status')->default('success')->comment('状态：success 成功，failed 失败');
            $table->timestamps();

            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_backups');
    }
};