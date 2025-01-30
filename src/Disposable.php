<?php

namespace CristianPeter\LaravelDisposableContactGuard;

interface Disposable
{
    public function isDisposable(string $item): bool;
    public function isNotDisposable(string $item): bool;
    public function setWhitelist(array $whitelist): static;
    public function getWhitelist(): array;
    public function setBlacklist(array $blacklist): static;
    public function getBlacklist(): array;
}
