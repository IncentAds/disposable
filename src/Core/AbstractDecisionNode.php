<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core;
use CristianPeter\LaravelDisposableContactGuard\Core\Nodes\DecisionNode;
use Illuminate\Support\Facades\Log;

abstract class AbstractDecisionNode
{
    protected int $retries = 1;
    protected int $timeout = 300;

    public function __construct(private readonly DecisionNode $then, private readonly DecisionNode $otherwise)
    {
    }

    public function resolve(mixed $state): mixed
    {
        while ($this->retries-- > 0) {
            try {
                $result = $this->then->handle($state);

                if ($this->validate($result)) {
                    return $result;
                }
            } catch (\Throwable $e) {
                Log::error("Error on node: " . $this->then->key(), [
                    'exception' => $e,
                    'retries_left' => $this->retries
                ]);
            }
        }

        return $this->otherwise->handle($state);
    }

    abstract public function handle(mixed $state): mixed;
    abstract protected function validate($result): bool;
}
