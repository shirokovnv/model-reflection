<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Facades;

use Illuminate\Support\Facades\Facade;
use Shirokovnv\ModelReflection\ReflectedModel;

/**
 * @method static ReflectedModel make(string $model_class_name)
 */
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
