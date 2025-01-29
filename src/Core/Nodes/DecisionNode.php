<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core\Nodes;

interface DecisionNode
{
    public function handle(string $state): mixed;

    public function key(): string;
}