<?php

namespace Incentads\Disposable\Core\Phone\Nodes;

use Incentads\Disposable\Facades\DisposableNumbers;

class StoragableListAdaptorNode implements PhoneAdaptorNode
{
    public function isNotDisposable(mixed $number): bool
    {
        return DisposableNumbers::isNotDisposable($number);
    }
}
