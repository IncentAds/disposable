<?php

namespace CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR;

use CristianPeter\LaravelDisposableContactGuard\Integrations\NumCheckR\Dto\NumCheckrDto;

interface NumCheckrApiInterface
{
    public function info(string $number): NumCheckrDto;

    public function isNotDisposable(string $number): bool;

}
