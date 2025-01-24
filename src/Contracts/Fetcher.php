<?php

namespace CristianPeter\LaravelDisposableContactGuard\Contracts;

interface Fetcher
{
    public function handle($url): array;
}
