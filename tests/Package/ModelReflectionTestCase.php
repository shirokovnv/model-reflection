<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Tests\Package;

use Illuminate\Support\Facades\DB;
use Shirokovnv\ModelReflection\ModelReflection;
use Shirokovnv\ModelReflection\Tests\TestCase;

abstract class ModelReflectionTestCase extends TestCase
{
    /**
     * @return ModelReflection
     *
     * @throws \Exception
     */
    public function getReflectionService(): ModelReflection
    {
        return new ModelReflection(DB::connection());
    }

    /**
     * @return string[]
     */
    public function getAvailableJsonKeys(): array
    {
        return [
            'name',
            'table_name',
            'fields',
            'relations',
            'foreign_keys',
            'scopes',
        ];
    }
}
