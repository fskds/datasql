<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 为 schema_types 增加「实例」字段
     */
    public function up(): void
    {
        Schema::table('schema_types', function (Blueprint $table) {
            $table->text('example')->nullable()->after('description')->comment('类型实例/示例');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schema_types', function (Blueprint $table) {
            $table->dropColumn('example');
        });
    }
};