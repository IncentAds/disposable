<?php

namespace Incentads\Disposable;

use Illuminate\Contracts\Cache\Repository as Cache;
use Incentads\Disposable\Cache\HasCache;
use Incentads\Disposable\Utils\HandleStorage;
use Psr\SimpleCache\InvalidArgumentException;

class DisposableNumbers implements Disposable
{
    use HandleStorage;
    use HasCache;

    public const string FALLBACK_LOCATION =  __DIR__ . '/../disposable_numbers.json';

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
        $this->numbers = $this->getFromCache($this->cacheKey) ?? [];
        if (empty($this->numbers) || $this->hasNewBlackListItem()) {
            $this->numbers = $this->combineSavingCache($this->getBlacklist(), $this->getFromStorage());
        }
        return $this;
    }

    /**
     * Checks whether the given email address' domain matches a disposable email service.
     *
     * @param string $item
     * @return bool
     */
    public function isDisposable(string $item): bool
    {
        if ( ! $item) {
            return false;
        }

        return in_array($item, $this->numbers);
    }

    /**
     * Checks whether the given email address' domain doesn't match a disposable email service.
     *
     * @param string $item
     * @return bool
     */
    public function isLegit(string $item): bool
    {
        return ! $this->isDisposable($item);
    }

    /**
     * Alias of "isLegit".
     *
     * @param string $number
     * @return bool
     */
    public function isNotDisposable(string $number): bool
    {
        return $this->isLegit($number);
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
     * Check if new domains were added to the blacklist
     */
    public function hasNewBlackListItem(): bool
    {
        return count(array_diff($this->getBlacklist(), $this->getNumbers()));
    }

    /**
     * Merge the arrays and save a result in cache
     * @param array ...$arrays
     * @return array
     */
    private function combineSavingCache(array ...$arrays): array
    {
        $merged = array_merge(...$arrays);
        $this->saveToCache($this->cacheKey, $merged);
        return $merged;
    }
}
