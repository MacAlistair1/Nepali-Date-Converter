<?php

namespace Jeeven\NepaliDateConverter\Facades;

use Illuminate\Support\Facades\Facade;

class NepaliDate extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'nepali-date';
    }
}
