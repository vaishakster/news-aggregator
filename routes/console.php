<?php

use App\Console\Commands\FetchArticles;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule the 'fetch:articles' command to run every six hours
Schedule::command('fetch:articles')->everyTwoMinutes();