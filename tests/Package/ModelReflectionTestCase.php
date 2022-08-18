<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Tests\Package;

use Illuminate\Support\Facades\DB;
use Shirokovnv\ModelReflection\Exceptions\ReflectionException;
use Shirokovnv\ModelReflection\ModelReflection;
use Shirokovnv\ModelReflection\Tests\TestCase;

abstract class ModelReflectionTestCase extends TestCase
{
    /**
     * @throws \Exception
     *
     * @return ModelReflection
     */
    protected function getReflectionService(): ModelReflection
    {
        return new ModelReflection(DB::connection());
    }

    /**
     * @return string[]
     */
    protected function getAvailableJsonKeys(): array
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

    /**
     * @param string $class_name
     *
     *@throws \ReflectionException|ReflectionException
     *
     * @return void
     */
    protected function assertBaseJsonStruct(string $class_name): void
    {
        $service = $this->getReflectionService();

        $json_struct = $service->reflect($class_name)->toArray();

        $this->assertIsArray($json_struct);

        foreach ($this->getAvailableJsonKeys() as $key) {
            $this->assertArrayHasKey($key, $json_struct);
        }
    }
}
