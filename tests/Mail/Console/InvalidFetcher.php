<?php

namespace Incentads\Disposable\Tests\Mail\Console;

class InvalidFetcher
{
    public function handle($url)
    {
        return $url;
    }
}
