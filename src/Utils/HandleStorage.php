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

        return array_diff(
            json_decode($items, true),
            $this->getWhitelist()
        );
    }
    /**
     * Save the domains in storage.
     */
    public function saveToStorage(array $items): bool|int
    {
        $saved = file_put_contents($this->getStoragePath(), json_encode($items));

        if ($saved) {
            $this->flushCache();
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