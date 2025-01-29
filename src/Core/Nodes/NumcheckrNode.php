<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core\Nodes;

use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Exceptions\ApiErrorException;
use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\NumCheckrManager;
use Illuminate\Http\Client\ConnectionException;

class NumcheckrNode implements DecisionNode
{
    /**
     * @throws ApiErrorException
     * @throws ConnectionException
     */
    public function handle(mixed $state): mixed
    {
        $manager = app(NumCheckrManager::class);
        return $manager->isDisposable($state);
    }


}
