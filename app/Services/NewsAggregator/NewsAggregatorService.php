<?php

namespace App\Services\NewsAggregator;
use Illuminate\Support\Facades\Cache;

class NewsAggregatorService
{
    protected $sources = [];

    public function __construct()
    {
        $this->sources['newsapi'] = new NewsAPIService();
        $this->sources['guardianapi'] = new GuardianService();
        $this->sources['nyt'] = new NewYorkTimesService();
    }

    public function fetchFromSource($source)
    {
        if (!isset($this->sources[$source])) {
            throw new \Exception("Source not supported");
        }

        $this->sources[$source]->fetchArticles();
    }

    public function fetchFromAllSources()
    {
        foreach ($this->sources as $source) {
            $source->fetchArticles();
        }
        $this->clearArticleCache();
    }

    protected function clearArticleCache()
    {
        Cache::forget('articles');

        for ($page = 1; $page <= 10; $page++) {
            Cache::forget("articles_page_{$page}");
        }
        Cache::forget('articles_search_*');
    }
}
