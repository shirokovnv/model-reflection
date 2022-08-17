<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Tests\Package;

use Shirokovnv\ModelReflection\Components\FieldRef;
use Shirokovnv\ModelReflection\Components\RelationRef;
use Shirokovnv\ModelReflection\Components\ScopeRef;
use Shirokovnv\ModelReflection\ReflectedModel;
use Shirokovnv\ModelReflection\Tests\User;

class UserReflectionTest extends ModelReflectionTestCase
{
    /**
     * @return void
     */
    public function testMakeReflection(): void
    {
        $service = $this->getReflectionService();

        $reflection = $service->reflect(User::class);
        $this->assertInstanceOf(ReflectedModel::class, $reflection);
        $this->assertEquals(User::class, $reflection->name);
        $this->assertEquals('users', $reflection->table_name);

        $user = $this->newUser();
        $user_attribute_keys = \array_keys($user->getAttributes());

        $this->assertEquals(7, $reflection->fields->count());
        foreach ($reflection->fields as $field) {
            $this->assertInstanceOf(FieldRef::class, $field);
            $this->assertContains($field->name, $user_attribute_keys);
        }

        $this->assertEquals(1, $reflection->relations->count());
        foreach ($reflection->relations as $relation) {
            $this->assertInstanceOf(RelationRef::class, $relation);
            $this->assertContains($relation->name, ['posts']);
        }

        $this->assertEquals(1, $reflection->scopes->count());
        foreach ($reflection->scopes as $scope) {
            $this->assertInstanceOf(ScopeRef::class, $scope);
            $this->assertContains($scope->name, ['Active']);
        }
    }

    /**
     * @return void
     */
    public function testJsonSchema(): void
    {
        $service = $this->getReflectionService();

        $json_schema = $service->reflect(User::class)->toArray();

        $this->assertIsArray($json_schema);

        foreach ($this->getAvailableJsonKeys() as $key) {
            $this->assertArrayHasKey($key, $json_schema);
        }
    }

    /**
     * @return User
     */
    private function newUser(): User
    {
        $user = new User;
        $user->name = 'John';
        $user->email = 'Doe';
        $user->password = bcrypt('password');
        $user->active = true;
        $user->save();

        return $user;
    }
}
