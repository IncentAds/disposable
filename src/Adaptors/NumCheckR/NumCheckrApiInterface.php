<?php

namespace CristianPeter\LaravelDisposableContactGuard\Adaptors\NumCheckR;

use CristianPeter\LaravelDisposableContactGuard\Adaptors\NumCheckR\Dto\NumCheckrDto;

interface NumCheckrApiInterface
{
    public function info(string $number): NumCheckrDto;

    public function isDisposable(string $number): bool;

}
