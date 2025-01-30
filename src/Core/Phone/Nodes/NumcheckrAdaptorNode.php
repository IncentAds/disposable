<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core\Phone\Nodes;

use CristianPeter\LaravelDisposableContactGuard\Facades\DisposableNumbers;
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
        $isNotDisposable = $manager->isNotDisposable($number);
        if (! $isNotDisposable) {
            $numberList = DisposableNumbers::getFromCache(DisposableNumbers::$cacheKey);
            if (! isset($numberList[$number])) {
                $numberList[$number] = $number;
                DisposableNumbers::saveToStorage($numberList);
            }
        }
        return $isNotDisposable;
    }
}
