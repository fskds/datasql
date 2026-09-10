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
        Schema::create('content_banner_column', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('banner_id')->comment('Banner ID');
            $table->unsignedBigInteger('column_id')->comment('栏目ID');
            $table->timestamps();

            $table->index('banner_id');
            $table->index('column_id');
            $table->unique(['banner_id', 'column_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_banner_column');
    }
};
