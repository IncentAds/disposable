<?php

namespace CristianPeter\LaravelDisposableContactGuard\Fetcher;

interface Fetcher
{
    public function handle($url): array;
}
