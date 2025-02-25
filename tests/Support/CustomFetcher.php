<?php

namespace Incentads\Disposable\Tests\Support;

use Incentads\Disposable\Fetcher\Fetcher;

class CustomFetcher implements Fetcher
{
    public function handle($url): array
    {
        return [$url];
    }
}
