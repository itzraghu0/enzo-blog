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
            $table->string('geo_country_code', 2)->nullable()->after('aeo_questions');
            $table->string('geo_region', 191)->nullable()->after('geo_country_code');
            $table->string('geo_city', 191)->nullable()->after('geo_region');
            $table->decimal('geo_latitude', 10, 7)->nullable()->after('geo_city');
            $table->decimal('geo_longitude', 10, 7)->nullable()->after('geo_latitude');
            $table->string('geo_timezone', 100)->nullable()->after('geo_longitude');

            $table->index(['geo_country_code', 'geo_region'], 'media_geo_country_region_index');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table): void {
            $table->dropIndex('media_geo_country_region_index');
            $table->dropColumn([
                'seo_keywords',
                'hashtags',
                'relevance_notes',
                'aeo_summary',
                'aeo_questions',
                'geo_country_code',
                'geo_region',
                'geo_city',
                'geo_latitude',
                'geo_longitude',
                'geo_timezone',
            ]);
        });
    }
};
