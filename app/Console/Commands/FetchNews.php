<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAggregator\NewsAggregatorService;

class FetchNews extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news articles from various sources';

    protected $newsAggregator;

    public function __construct(NewsAggregatorService $newsAggregator)
    {
        parent::__construct();
        $this->newsAggregator = $newsAggregator;
    }

    public function handle()
    {
        $this->info('Fetching news articles...');
        $this->newsAggregator->fetchFromAllSources();
        $this->info('News articles fetched successfully.');
    }
}
