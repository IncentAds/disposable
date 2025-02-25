<?php

namespace Incentads\Disposable\Tests\Support;

class InvalidFetcher
{
    public function handle($url)
    {
        return $url;
    }
}
