<?php

namespace CristianPeter\LaravelDisposableContactGuard\Facades;

use Illuminate\Support\Facades\Facade;

class DisposableNumbers extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'disposable_phone.numbers';
    }
}
