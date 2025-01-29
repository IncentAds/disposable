<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core\Nodes;

use CristianPeter\LaravelDisposableContactGuard\Facades\DisposableNumbers;

class StoragableListNode implements DecisionNode
{
    public function handle(mixed $state): mixed
    {
        return DisposableNumbers::isNotDisposable($state);
    }
}
