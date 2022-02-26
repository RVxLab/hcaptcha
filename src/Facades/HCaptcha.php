<?php

declare(strict_types=1);

namespace Scyllaly\HCaptcha\Facades;

use Illuminate\Support\Facades\Facade;

class HCaptcha extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'HCaptcha';
    }
}
