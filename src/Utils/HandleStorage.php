<?php

namespace Incentads\Disposable\Utils;

trait HandleStorage
{
    /**
     * Save the domains in storage.
     */
    public function saveToStorage(array $items): bool|int
    {
        $saved = file_put_contents($this->getStoragePath(), json_encode(array_values($items)));

        // overwrite key in cache
        if ($saved) {
            $this->saveToCache($this->cacheKey, array_values($items));
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
        $items = array_diff(json_decode($items), $this->getWhitelist());
        return ArrayHelper::combineKeysValues($items) ?: [];
    }
}
