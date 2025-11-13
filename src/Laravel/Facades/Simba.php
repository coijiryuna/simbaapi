<?php

namespace simba\api\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

class Simba extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'simba';
    }
}
