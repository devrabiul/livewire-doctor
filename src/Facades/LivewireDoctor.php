<?php

namespace Devrabiul\LivewireDoctor\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static initCustomAsset():void
 */
class LivewireDoctor extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'LivewireDoctor';
    }
}
