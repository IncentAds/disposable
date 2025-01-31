<?php

namespace CristianPeter\LaravelDisposableContactGuard\Utils;

trait HandleStorage
{
    /**
     * Get the domains from storage, or if non-existent, from the package.
     *
     * @return array
     */
    protected function getFromStorage(): array
    {
        $items = is_file($this->getStoragePath())
            ? file_get_contents($this->getStoragePath())
            : file_get_contents(self::FALLBACK_LOCATION);
        $items = ArrayHelper::combineKeysValues(json_decode($items)) ?: [];
        return array_diff($items, $this->getWhitelist());
    }
    /**
     * Save the domains in storage.
     */
    public function saveToStorage(array $items): bool|int
    {
        $saved = file_put_contents($this->getStoragePath(), json_encode(array_values($items)));

        // overwrite key in cache
        if ($saved) {
            $this->saveToCache($this->cacheKey, $items);
        }

        return $saved;
    }

    /**
     * Flushes the source's list if applicable.
     */
    public function flushStorage(): void
    {
        if (is_file($this->getStoragePath())) {
            @unlink($this->getStoragePath());
        }
    }
}
