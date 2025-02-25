<?php

namespace Incentads\Disposable\Integrations\NumCheckR\Client;

interface NumCheckrClientInterface
{
    public function post(array $data): array;

    public function isDisposable(string $number): bool;

}
