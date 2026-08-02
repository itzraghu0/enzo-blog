<?php

namespace App\Jobs;

use App\Services\JtlBlogImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ImportJtlBlogsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    public int $timeout = 0;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(private readonly array $options)
    {
        $this->onQueue('imports');
    }

    public function handle(JtlBlogImportService $importer): void
    {
        $result = $importer->import($this->options);

        foreach ($result['logs'] as $line) {
            Log::info($line);
        }

        if (!$result['success']) {
            throw new RuntimeException('JTL blog import failed. Check the application log for importer output.');
        }
    }
}
