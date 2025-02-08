<?php

namespace App\Services\NewsAggregator;

interface NewsSourceInterface
{
    public function fetchArticles();
}
