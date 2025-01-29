<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core;

class AdaptorDecisionNode extends AbstractDecisionNode
{
    protected int $retries = 1;

    protected function resolve(): void
    {
        $this->handle();
    }

    protected function validate($result): bool
    {
        return true;
    }
}
