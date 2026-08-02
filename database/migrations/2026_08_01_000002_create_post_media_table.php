<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_media', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('collection', 100)->default('default');
            $table->string('locale', 10)->nullable();
            $table->string('purpose', 100)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['post_id', 'media_id', 'collection'], 'post_media_post_media_collection_unique');
            $table->index(['post_id', 'collection', 'sort_order'], 'post_media_post_collection_sort_index');
            $table->index(['media_id', 'collection'], 'post_media_media_collection_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_media');
    }
};
