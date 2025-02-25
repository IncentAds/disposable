<?php

namespace Incentads\Disposable\Integrations\NumCheckR;

use Incentads\Disposable\Integrations\NumCheckR\Dto\NumCheckrDto;

interface NumCheckrApiInterface
{
    public function info(string $number): NumCheckrDto;

    public function isNotDisposable(string $number): bool;

    public function isDisposable(string $number): bool;

}
