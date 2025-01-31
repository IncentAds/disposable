<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core\Phone\Nodes;

use CristianPeter\LaravelDisposableContactGuard\DisposableNumbers;
use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Exceptions\ApiErrorException;
use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\NumCheckrManager;
use Illuminate\Http\Client\ConnectionException;
use Psr\SimpleCache\InvalidArgumentException;

class NumcheckrAdaptorNode implements PhoneAdaptorNode
{
    /**
     * @throws ApiErrorException
     * @throws ConnectionException
     * @throws InvalidArgumentException
     */
    public function isNotDisposable(mixed $number): bool
    {
        $manager = app(NumCheckrManager::class);
        $isNotDisposable = $manager->isNotDisposable($number);
        if (! $isNotDisposable) {
            $disposableNumbers = app(DisposableNumbers::class);
            $numberList = $disposableNumbers->getFromCache($disposableNumbers->getCacheKey());
            if (! isset($numberList[$number])) {
                $numberList[$number] = $number;
                $disposableNumbers->saveToStorage($numberList);
            }
        }
        return $isNotDisposable;
    }
}
