<?php

namespace Incentads\Disposable\Core\Phone\Nodes;

use Incentads\Disposable\Facades\DisposableNumbers;

class StorableListAdaptorNode implements PhoneAdaptorNode
{
    public function isNotDisposable(mixed $number): bool
    {
        return DisposableNumbers::isLegit($number);
    }
}
