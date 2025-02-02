<?php

namespace CristianPeter\LaravelDisposableContactGuard;

use CristianPeter\LaravelDisposableContactGuard\Cache\HasCache;
use CristianPeter\LaravelDisposableContactGuard\Utils\HandleStorage;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

class DisposableDomains implements Disposable
{
    use HasCache;
    use HandleStorage;

    const string FALLBACK_LOCATION =  __DIR__ . '/../disposable_domains.json';
    /**
     * The storage path to retrieve from and save to.
     *
     * @var string
     */
    protected string $storagePath;

    /**
     * Array of retrieved disposable domains.
     *
     * @var array
     */
    protected array $domains = [];

    /**
     * The whitelist of domains to allow.
     *
     * @var array
     */
    protected array $whitelist = [];

    /**
     * The blacklist of domains to not allow.
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
     * Whether to include subdomains.
     *
     * @var bool
     */
    protected bool $includeSubdomains = false;

    public function __construct(?CacheInterface $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Loads the domains from cache/storage into the class.
     *
     * @return $this
     * @throws InvalidArgumentException
     */
    public function bootstrap(): static
    {
        $this->domains = $this->getFromCache($this->cacheKey) ?? [];

        if (empty($this->domains)) {
            $this->domains = $this->combineSavingCache($this->getBlacklist(), $this->getFromStorage());
        } else if ($this->hasNewBlackListItem()) {
            $this->domains = array_merge($this->domains, $this->getBlacklist());
            $this->saveToCache($this->cacheKey, $this->domains);
        }else if ($this->hasNewWhitelistItem()){
            $this->domains = array_diff($this->domains, $this->getWhitelist());
            $this->saveToCache($this->cacheKey, $this->domains);
        }

        return $this;
    }

    /**
     * Checks whether the given email address' domain matches a disposable email service.
     *
     * @param string $email
     * @return bool
     */
    public function isDisposable($email): bool
    {
        $domain = Str::lower(Arr::get(explode('@', $email, 2), 1));

        if (! $domain) {
            // Just ignore this validator if the value doesn't even resemble an email or domain.
            return false;
        }

        if (in_array($domain, $this->domains)) {
            // Domain is a matching root domain.
            return true;
        }

        if ($this->getIncludeSubdomains()) {
            // Check for subdomains.
            foreach ($this->domains as $root) {
                if (str_ends_with($domain, '.' . $root)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Checks whether the given email address' domain doesn't match a disposable email service.
     *
     * @param string $email
     * @return bool
     */
    public function isNotDisposable(string $email): bool
    {
        return ! $this->isDisposable($email);
    }

    /**
     * Alias of "isNotDisposable".
     *
     * @param string $email
     * @return bool
     */
    public function isIndisposable(string $email): bool
    {
        return $this->isNotDisposable($email);
    }

    /**
     * Get the list of disposable domains.
     *
     * @return array
     */
    public function getDomains(): array
    {
        return $this->domains;
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
     * Get whether to include subdomains.
     *
     * @return bool
     */
    public function getIncludeSubdomains(): bool
    {
        return $this->includeSubdomains;
    }

    /**
     * Set whether to include subdomains.
     *
     * @return $this
     */
    public function setIncludeSubdomains(bool $includeSubdomains): static
    {
        $this->includeSubdomains = $includeSubdomains;

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
        $this->saveToCache($this->cacheKey, $merged);
        return $merged;
    }

    /**
     * Check if new domains was added to the blacklist
     */
    public function hasNewBlackListItem(): bool
    {
        return count(array_diff($this->getBlacklist(), $this->getDomains()));
    }

    /**
     * Check if new domains was added to the blacklist
     */
    public function hasNewWhitelistItem(): bool
    {
        return count(array_diff($this->getWhitelist(), $this->getDomains()));
    }
}
