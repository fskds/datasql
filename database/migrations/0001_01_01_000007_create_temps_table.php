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
        Schema::create('attachment_temps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('文件名');
            $table->string('path')->comment('文件路径');
            $table->unsignedBigInteger('size')->default(0)->comment('文件大小(字节)');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachment_temps');
    }
};
