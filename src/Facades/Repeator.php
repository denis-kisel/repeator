<?php

namespace DenisKisel\Repeator\Facades;

use Illuminate\Support\Facades\Facade;

class Repeator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'repeator';
    }
}
