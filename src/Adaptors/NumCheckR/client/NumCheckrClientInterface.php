<?php

namespace CristianPeter\LaravelDisposableContactGuard\Adaptors\NumCheckR\client;

interface NumCheckrClientInterface
{
    public function post(array $data): array;

    public function isDisposable(string $number): bool;

}