<?php

namespace CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Client;

interface NumCheckrClientInterface
{
    public function post(array $data): array;

    public function isDisposable(string $number): bool;

}