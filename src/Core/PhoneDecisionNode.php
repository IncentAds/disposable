<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core;

class PhoneDecisionNode extends AbstractDecisionNode
{
    public function handle($state): mixed
    {
        return $this->resolve($state);
    }

    protected function validate(mixed $result): bool
    {
        return $result === true;
    }
}
