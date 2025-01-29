<?php

namespace CristianPeter\LaravelDisposableContactGuard\Core;
use Illuminate\Support\Facades\Log;

abstract class AbstractDecisionNode
{
    public function __construct(private readonly array $nodes)
    {
    }

    public function resolve(mixed $state): mixed
    {
        foreach ($this->nodes as $node) {
            try {
                $result = $node->handle($state);

                if ($this->validate($result)) {
                    return $result;
                }
            } catch (\Throwable $e) {
                Log::error("Error on node: " . get_class($node), [
                    'exception' => $e,
                ]);
            }
        }
        return false;
    }

    abstract public function handle(mixed $state): mixed;
    abstract protected function validate($result): bool;
}
