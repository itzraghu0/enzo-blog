<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('status', 50)->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['parent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
