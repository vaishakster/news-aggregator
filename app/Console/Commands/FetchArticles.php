<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Services\NewsAPIService;
use App\Services\GuardianService;
use App\Services\NYTService;

class FetchArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from external news sources and store them in the database';

    protected $newsAPIService;
    protected $guardianService;
    protected $nytService;


    public function __construct(NewsAPIService $newsAPIService, GuardianService $guardianService, NYTService $nytService)
    {
        parent::__construct();
        $this->newsAPIService = $newsAPIService;
        $this->guardianService = $guardianService;
        $this->nytService = $nytService;
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching articles from NewsAPI...');
        $this->fetchAndStore($this->newsAPIService->fetchArticles(), 'NewsAPI');

        $this->info('Fetching articles from The Guardian...');
        $this->fetchAndStore($this->guardianService->fetchArticles(), 'Guardian');

        $this->info('Fetching articles from New York Times...');
        $this->fetchAndStore($this->nytService->fetchArticles(), 'NYT');

        $this->info('Articles fetched and stored successfully.');
    }

    /**
     * Process and store articles in the database.
     */
    protected function fetchAndStore(array $articles, $author)
    {
        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['title' => $article['title']], // Use title as unique identifier
                [
                    'title' => $article['title'],
                    'content' => $article['description'] ?? '',
                    'category' => $article['category'] ?? 'general',
                    'source' => $article['source']['name'] ?? 'Unknown',
                    'author' => $author
                ]
            );
        }
    }
}
