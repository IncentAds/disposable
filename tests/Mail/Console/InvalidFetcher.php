<?php

namespace CristianPeter\LaravelDisposableContactGuard\Tests\mail\Console;

class InvalidFetcher
{
    public function handle($url)
    {
        return $url;
    }
}
