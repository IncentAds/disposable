<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core;
use Illuminate\Support\Facades\Log;

abstract class AbstractDecisionNode implements DecisionNode
{
    protected int $retries = 1;
    protected int $timeout = 300;

    public function __construct(protected DecisionNode $then, protected DecisionNode $otherwise)
    {
    }

    public function handle(): mixed
    {
        while ($this->retries-- > 0) {
            try {
                $result = $this->then->handle();

                if ($this->validate($result)) {
                    return $result;
                }
            } catch (\Throwable $e) {
                Log::error("Error en handle(): " . $e->getMessage(), [
                    'exception' => $e,
                    'retries_left' => $this->retries
                ]);
            }
        }

        return $this->otherwise->handle();
    }

    abstract protected function resolve(): void;
    abstract protected function validate($result): bool;
}
