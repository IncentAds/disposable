<?php

namespace CristianPeter\LaravelDisposableContactGuard\Facades;

use Illuminate\Support\Facades\Facade;

class DisposableDomains extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'disposable_email.domains';
    }
}
