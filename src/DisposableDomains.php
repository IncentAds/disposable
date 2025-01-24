<?php

namespace CristianPeter\LaravelDisposableContactGuard;

use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\SimpleCache\InvalidArgumentException;

class DisposableDomains
{
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

    /**
     * Disposable constructor.
     */
    public function __construct(?Cache $cache = null)
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
        $domains = $this->getFromCache();

        if (! $domains) {
            $this->saveToCache(
                $domains = $this->getFromStorage()
            );
        }

        $this->domains = $domains;

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

                return null;
            }

            return $domains;
        }

        return null;
    }

    /**
     * Save the domains in cache.
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
     * Get the domains from storage, or if non-existent, from the package.
     *
     * @return array
     */
    protected function getFromStorage(): array
    {
        $storageDomains = is_file($this->getStoragePath())
            ? file_get_contents($this->getStoragePath())
            : file_get_contents(__DIR__.'/../domains.json');

        $storageDomainsArray = json_decode($storageDomains, true);

        // Combine the blacklist with the storage domains
        $allDomains = array_merge($this->getBlacklist(), $storageDomainsArray);

        // Return the combined array excluding the whitelisted domains
        return array_diff($allDomains, $this->getWhitelist());
    }

    /**
     * Save the domains in storage.
     */
    public function saveToStorage(array $domains): bool|int
    {
        $saved = file_put_contents($this->getStoragePath(), json_encode($domains));

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
     * @param  string  $email
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
                if (str_ends_with($domain, '.'.$root)) {
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
}
