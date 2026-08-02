<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\ImportJtlBlogsJob;
use App\Services\JtlBlogImportService;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('jtl:import-blogs
    {--database=cupssy : Source JTL database name}
    {--username=root : Source JTL database username}
    {--password=root : Source JTL database password}
    {--source-root= : JTL public/source root used for media copying}
    {--chunk=1000 : Number of rows processed per chunk}
    {--preserve-ids : Preserve source IDs in target blog tables}
    {--media-root= : Target public media directory for copied JTL content images; defaults to blog.media_directory}
    {--public-prefix=/ : Public URL prefix for rewritten image paths}
    {--dry-run : Validate and count without writing rows}
    {--connection=database : Queue connection used when dispatching the import}
    {--sync : Run immediately instead of dispatching a queued job}',
    function (JtlBlogImportService $importer): int {
        $options = [
            'database' => (string) $this->option('database'),
            'username' => (string) $this->option('username'),
            'password' => (string) $this->option('password'),
            'source_root' => (string) ($this->option('source-root') ?: base_path()),
            'chunk' => (int) $this->option('chunk'),
            'preserve_ids' => (bool) $this->option('preserve-ids'),
            'media_root' => (string) $this->option('media-root'),
            'public_prefix' => (string) $this->option('public-prefix'),
            'dry_run' => (bool) $this->option('dry-run'),
        ];

        if (!$this->option('sync')) {
            ImportJtlBlogsJob::dispatch($options)->onConnection((string) $this->option('connection'));
            $this->info('JTL blog import job dispatched on the imports queue.');

            return self::SUCCESS;
        }

        $result = $importer->import($options);

        foreach ($result['logs'] as $line) {
            if (str_starts_with($line, '[warning]')) {
                $this->warn(substr($line, 10));
                continue;
            }

            $this->line($line);
        }

        return $result['success'] ? self::SUCCESS : self::FAILURE;
    }
)->purpose('Import JTL blog data into the Laravel blog tables');
