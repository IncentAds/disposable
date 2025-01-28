<?php

namespace CristianPeter\LaravelDisposableContactGuard;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class DisposableNumbers
{
    /**
     * The storage path to retrieve from and save to.
     *
     * @var string
     */
    protected string $storagePath;

    /**
     * Array of retrieved disposable numbers.
     *
     * @var array
     */
    protected array $numbers = [];

    /**
     * The whitelist of numbers to allow.
     *
     * @var array
     */
    protected array $whitelist = [];

    /**
     * The blacklist of numbers to not allow.
     *
     * @var array
     */
    protected array $blacklist = [];

    /**
     * The cache repository.
     *
     * @var Cache|null
     */
    protected ?Cache $cache;

    /**
     * The cache key.
     *
     * @var string
     */
    protected string $cacheKey;


    /**
     * Disposable constructor.
     */
    public function __construct(?Cache $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Loads the numbers from cache/storage into the class.
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function bootstrap(): static
    {
        $this->numbers = $this->getFromCache() ?? [];
        if (empty($this->numbers) || $this->hasNewBlackListItem()) {
            $this->numbers = $this->combineSavingCache($this->getBlacklist(), $this->getFromStorage());
        }
        return $this;
    }

    /**
     * Get the domains from cache.
     *
     * @return array|null
     * @throws InvalidArgumentException
     */
    protected function getFromCache(): ?array
    {
        if ($this->cache) {
            $domains = $this->cache->get($this->getCacheKey());

            // @TODO: Legacy code for bugfix. Remove me.
            if (is_string($domains) || empty($domains)) {
                $this->flushCache();

                return [];
            }

            return $domains;
        }

        return [];
    }

    /**
     * Save the numbers in cache.
     */
    public function saveToCache(?array $domains = null): void
    {
        if ($this->cache && ! empty($domains)) {
            $this->cache->forever($this->getCacheKey(), $domains);
        }
    }

    /**
     * Flushes the cache if applicable.
     */
    public function flushCache(): void
    {
        $this->cache?->forget($this->getCacheKey());
    }

    /**
     * Get the numbers from storage, or if non-existent, from the package.
     *
     * @return array
     */
    protected function getFromStorage(): array
    {
        $domains = is_file($this->getStoragePath())
            ? file_get_contents($this->getStoragePath())
            : file_get_contents(__DIR__ . '/../domains.json');

        return array_diff(
            json_decode($domains, true),
            $this->getWhitelist()
        );
    }

    /**
     * Save the domains in storage.
     */
    public function saveToStorage(array $numbers): bool|int
    {
        $saved = file_put_contents($this->getStoragePath(), json_encode($numbers));

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

    /**
     * Checks whether the given email address' domain matches a disposable email service.
     *
     * @param string $number
     * @return bool
     */
    public function isDisposable($number): bool
    {
        if (! $number) {
            return false;
        }
        if (in_array($number, $this->numbers)) {
            // Domain is a matching root domain.
            return true;
        }
        return false;
    }

    /**
     * Checks whether the given email address' domain doesn't match a disposable email service.
     *
     * @param string $email
     * @return bool
     */
    public function isNotDisposable(string $number): bool
    {
        return ! $this->isDisposable($number);
    }

    /**
     * Alias of "isNotDisposable".
     *
     * @param string $number
     * @return bool
     */
    public function isIndisposable(string $number): bool
    {
        return $this->isNotDisposable($number);
    }

    /**
     * Get the list of disposable domains.
     *
     * @return array
     */
    public function getNumbers(): array
    {
        return $this->numbers;
    }

    /**
     * Get the whitelist.
     *
     * @return array
     */
    public function getWhitelist(): array
    {
        return $this->whitelist;
    }

    /**
     * Get the blacklist.
     *
     * @return array
     */
    public function getBlacklist(): array
    {
        return $this->blacklist;
    }

    /**
     * Set the whitelist.
     *
     * @return $this
     */
    public function setWhitelist(array $whitelist): static
    {
        $this->whitelist = $whitelist;

        return $this;
    }

    /**
     * Set the blacklist.
     *
     * @return $this
     */
    public function setBlacklist(array $blacklist): static
    {
        $this->blacklist = $blacklist;

        return $this;
    }

    /**
     * Get the storage path.
     *
     * @return string
     */
    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    /**
     * Set the storage path.
     *
     * @param string $path
     * @return $this
     */
    public function setStoragePath(string $path): static
    {
        $this->storagePath = $path;

        return $this;
    }

    /**
     * Get the cache key.
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * Set the cache key.
     *
     * @param string $key
     * @return $this
     */
    public function setCacheKey(string $key): static
    {
        $this->cacheKey = $key;

        return $this;
    }

    /**
     * Merge the arrays and save result in cache
     * @param array ...$arrays
     * @return array
     */
    private function combineSavingCache(array ...$arrays): array
    {
        $merged = array_merge(...$arrays);
        $this->saveToCache($merged);
        return $merged;
    }

    /**
     * Check if new domains was added to the blacklist
     */
    public function hasNewBlackListItem(): bool
    {
        return count(array_diff($this->getBlacklist(), $this->getNumbers()));
    }
}
