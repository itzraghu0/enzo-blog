<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('disk', 191)->default('public');
            $table->string('path', 191);
            $table->string('filename', 191);
            $table->string('original_name', 191);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->string('alt_text')->nullable();
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->string('collection', 80)->default('default');
            $table->string('locale', 10)->nullable();
            $table->string('mediable_type', 120)->nullable();
            $table->unsignedBigInteger('mediable_id')->nullable();
            $table->index(['mediable_type', 'mediable_id']);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['collection', 'locale']);
            $table->index(['collection', 'mediable_type', 'mediable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
