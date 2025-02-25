<?php

namespace Incentads\Disposable\Core\Phone;

use Exception;
use Illuminate\Support\Facades\Log;

final readonly class PhoneDecisionNode
{
    public function __construct(private array $nodes) {}

    /**
     * Entry point for starting calling nodes
     * @param mixed $state
     * @return bool
     */
    public function handle(mixed $state): bool
    {
        return $this->resolve($state);
    }

    /**
     * Evaluates all nodes in the configured order. If any node returns false, it returns false;
     * otherwise, it returns true.
     * @param mixed $state
     * @return mixed
     */
    private function resolve(mixed $state): mixed
    {
        foreach ($this->nodes as $node) {
            try {
                if ( ! $node->isNotDisposable($state)) {
                    return false;
                }
            } catch (Exception $e) {
                Log::error("Error on node: " . get_class($node), ['exception' => $e]);
            }
        }

        return true;
    }
}
