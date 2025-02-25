<?php

namespace Incentads\Disposable\Core\Phone\Nodes;

interface PhoneAdaptorNode
{
    public function isNotDisposable(string $number): bool;

}
