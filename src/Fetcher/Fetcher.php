<?php

namespace Incentads\Disposable\Fetcher;

interface Fetcher
{
    public function handle($url): array;
}
