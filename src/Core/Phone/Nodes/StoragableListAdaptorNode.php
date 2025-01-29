<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core\Phone\Nodes;

use CristianPeter\LaravelDisposableContactGuard\Facades\DisposableNumbers;

class StoragableListAdaptorNode implements PhoneAdaptorNode
{
    public function isNotDisposable(mixed $number): bool
    {
        return DisposableNumbers::isNotDisposable($number);
    }
}
