<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core\Phone\Nodes;

use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Exceptions\ApiErrorException;
use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\NumCheckrManager;
use Illuminate\Http\Client\ConnectionException;

class NumcheckrAdaptorNode implements PhoneAdaptorNode
{
    /**
     * @throws ApiErrorException
     * @throws ConnectionException
     */
    public function isNotDisposable(mixed $number): bool
    {
        $manager = app(NumCheckrManager::class);
        return $manager->isNotDisposable($number);
    }
}
