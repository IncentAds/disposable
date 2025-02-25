<?php

namespace Incentads\Disposable\Tests\Mail\Console;

use Incentads\Disposable\Fetcher\Fetcher;

class CustomFetcher implements Fetcher
{
    public function handle($url): array
    {
        return [$url];
    }
}
