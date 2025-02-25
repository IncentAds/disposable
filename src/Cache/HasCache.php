<?php

namespace Incentads\Disposable\Cache;

use Incentads\Disposable\Utils\ArrayHelper;
use Psr\SimpleCache\InvalidArgumentException;

trait HasCache
{
    /**
     * Get the domains from cache.
     *
     * @throws InvalidArgumentException
     */
    public function getFromCache(string $key): ?array
    {
        if ($this->cache) {
            $items = $this->cache->get($key);

            // @TODO: Legacy code for bugfix. Remove me.
            if (is_string($items) || empty($items)) {
                $this->flushCache();

                return [];
            }

            return ArrayHelper::combineKeysValues($items);
        }

        return [];
    }

    /**
     * Save the domains in cache.
     */
    public function saveToCache(string $key, ?array $items = null): void
    {
        if ($this->cache && ! empty($items)) {
            $this->cache->forever($key, array_values($items));
        }
    }

    /**
     * Flushes the cache if applicable.
     */
    public function flushCache(): void
    {
        $this->cache?->forget($this->getCacheKey());
    }

}
