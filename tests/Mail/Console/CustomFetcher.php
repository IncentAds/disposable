<?php

namespace CristianPeter\LaravelDisposableContactGuard\Tests\Mail\Console;

use CristianPeter\LaravelDisposableContactGuard\Fetcher\Fetcher;

class CustomFetcher implements Fetcher
{
    public function handle($url): array
    {
        return [$url];
    }
}
