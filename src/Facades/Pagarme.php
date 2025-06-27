<?php

namespace Anisotton\Pagarme\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Anisotton\Pagarme\Pagarme
 */
class Pagarme extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Anisotton\Pagarme\Pagarme::class;
    }
}
