<?php

namespace CristianPeter\LaravelDisposableContactGuard\Tests\mail\Console;

use CristianPeter\LaravelDisposableContactGuard\Contracts\Fetcher;

class CustomFetcher implements Fetcher
{
    public function handle($url): array
    {
        return [$url];
    }
}
