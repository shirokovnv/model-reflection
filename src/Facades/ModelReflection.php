<?php

namespace Shirokovnv\ModelReflection\Facades;

use Illuminate\Support\Facades\Facade;

class ModelReflection extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'model-reflection';
    }
}
