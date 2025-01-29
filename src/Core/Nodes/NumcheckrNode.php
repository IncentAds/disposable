<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core\Nodes;

use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\NumCheckrManager;

class NumcheckrNode implements DecisionNode
{

    public function key(): string
    {
        return 'numcheckr';
    }

    public function handle(mixed $state): mixed
    {
        $manager = app(NumCheckrManager::class);
        return $manager->isDisposable($state);
    }


}
