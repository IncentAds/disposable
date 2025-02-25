<?php

namespace Incentads\Disposable\Core\Phone\Nodes;

use Illuminate\Http\Client\ConnectionException;
use Incentads\Disposable\DisposableNumbers;
use Incentads\Disposable\Integrations\NumCheckR\Exceptions\ApiErrorException;
use Incentads\Disposable\Integrations\NumCheckR\NumCheckrManager;
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
        if ( ! $isNotDisposable) {
            $disposableNumbers = app(DisposableNumbers::class);
            $numberList = $disposableNumbers->getFromCache($disposableNumbers->getCacheKey());
            if ( ! isset($numberList[$number])) {
                $numberList[$number] = $number;
                $disposableNumbers->saveToStorage($numberList);
            }
        }
        return $isNotDisposable;
    }
}
