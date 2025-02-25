<?php

namespace Incentads\Disposable\Facades;

use Illuminate\Support\Facades\Facade;

class DisposableDomains extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'disposable_email.domains';
    }
}
