<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('post_category', function (Blueprint $table): void {
            $table->index(['category_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::table('post_category', function (Blueprint $table): void {
            $table->dropIndex(['category_id', 'post_id']);
        });
    }
};
