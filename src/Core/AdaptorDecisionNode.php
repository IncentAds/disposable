<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core;

class AdaptorDecisionNode extends AbstractDecisionNode
{
    public function handle($state): mixed
    {
        return $this->resolve($state);
    }

    protected function validate(mixed $result): bool
    {
        return ! is_null($result);
    }
}
