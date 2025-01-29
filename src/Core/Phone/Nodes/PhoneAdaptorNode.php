<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core\Phone\Nodes;

interface PhoneAdaptorNode
{
    public function isNotDisposable(string $number): bool;

}