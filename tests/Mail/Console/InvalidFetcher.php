<?php

namespace CristianPeter\LaravelDisposableContactGuard\Tests\Mail\Console;

class InvalidFetcher
{
    public function handle($url)
    {
        return $url;
    }
}
