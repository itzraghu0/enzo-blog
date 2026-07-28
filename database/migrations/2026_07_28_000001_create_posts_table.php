<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 50)->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->string('country_code', 2)->nullable()->index();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
