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
        Schema::create('content_banner_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banner_id')->comment('Banner ID');
            $table->unsignedBigInteger('category_id')->comment('分类ID');
            $table->timestamps();

            $table->index('banner_id');
            $table->index('category_id');
            $table->unique(['banner_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_banner_category');
    }
};
