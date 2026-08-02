<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table): void {
            $table->text('geo_summary')->nullable()->after('aeo_questions');
            $table->text('geo_entities')->nullable()->after('geo_summary');
            $table->text('geo_prompts')->nullable()->after('geo_entities');
            $table->text('geo_context')->nullable()->after('geo_prompts');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table): void {
            $table->dropColumn([
                'geo_summary',
                'geo_entities',
                'geo_prompts',
                'geo_context',
            ]);
        });
    }
};
