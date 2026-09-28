<?php

namespace App\Console\Commands;

use App\Services\NewsImporterService;
use Illuminate\Console\Command;
use Throwable;

class DailyNewsScan extends Command
{
    protected $signature = 'news:scan {--json : Output the run report as JSON}';

    protected $description = 'Import new articles from the configured news RSS/Atom sources';

    public function __construct(private readonly NewsImporterService $importer)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Scanning news sources...');

        try {
            $report = $this->importer->import();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('json')) {
            $this->line((string) json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            return $report['failures'] === [] ? self::SUCCESS : self::SUCCESS;
        }

        $this->newLine();
        $this->table(
            ['Source', 'Status', 'Articles'],
            array_map(fn (array $s) => [
                $s['name'],
                $s['ok'] ? '<fg=green>OK</>' : '<fg=red>FAIL</>',
                $s['ok'] ? (string) $s['count'] : $s['error'],
            ], $report['sources'])
        );

        $this->newLine();
        $this->info("Fetched : {$report['total']} unique items");
        $this->info("Imported: {$report['imported']} new articles");
        $this->info("Skipped : {$report['skipped']} already stored");

        if ($report['failed_rows'] > 0) {
            $this->warn("Rows rejected: {$report['failed_rows']}");
        }

        if ($report['failures'] !== []) {
            $this->newLine();
            $this->warn(count($report['failures']).' source(s) could not be reached:');
            foreach ($report['failures'] as $failure) {
                $this->line("  - {$failure['name']}: {$failure['error']}");
            }
        }

        $this->newLine();
        $this->line("Finished in {$report['duration']}s");

        return self::SUCCESS;
    }
}
