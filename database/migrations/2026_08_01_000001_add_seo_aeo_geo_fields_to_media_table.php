<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table): void {
            $table->text('seo_keywords')->nullable()->after('caption');
            $table->text('hashtags')->nullable()->after('seo_keywords');
            $table->text('relevance_notes')->nullable()->after('hashtags');
            $table->text('aeo_summary')->nullable()->after('relevance_notes');
            $table->json('aeo_questions')->nullable()->after('aeo_summary');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table): void {
            $table->dropColumn([
                'seo_keywords',
                'hashtags',
                'relevance_notes',
                'aeo_summary',
                'aeo_questions',
            ]);
        });
    }
};