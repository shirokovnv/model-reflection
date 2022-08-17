<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Tests\Package;

use Shirokovnv\ModelReflection\Components\FieldRef;
use Shirokovnv\ModelReflection\Components\FkeyRef;
use Shirokovnv\ModelReflection\Components\RelationRef;
use Shirokovnv\ModelReflection\ReflectedModel;
use Shirokovnv\ModelReflection\Tests\Post;

class PostReflectionTest extends ModelReflectionTestCase
{
    public function testReflectionStruct(): void
    {
        $service = $this->getReflectionService();

        $reflection = $service->reflect(Post::class);
        $this->assertInstanceOf(ReflectedModel::class, $reflection);
        $this->assertEquals(Post::class, $reflection->name);
        $this->assertEquals('posts', $reflection->table_name);

        $post = $this->newPost();
        $post_attribute_keys = \array_keys($post->getAttributes());

        $this->assertEquals(6, $reflection->fields->count());
        foreach ($reflection->fields as $field) {
            $this->assertInstanceOf(FieldRef::class, $field);
            $this->assertContains($field->name, $post_attribute_keys);
        }

        $this->assertEquals(1, $reflection->relations->count());
        foreach ($reflection->relations as $relation) {
            $this->assertInstanceOf(RelationRef::class, $relation);
            $this->assertContains($relation->name, ['user']);
        }

        $this->assertEquals(1, $reflection->foreign_keys->count());
        foreach ($reflection->foreign_keys as $foreign_key) {
            $this->assertInstanceOf(FkeyRef::class, $foreign_key);
            $this->assertContains($foreign_key->name, ['user_id']);
        }

        $this->assertEquals(0, $reflection->scopes->count());
    }

    /**
     * @return void
     */
    public function assertBaseJsonStruct(string $class_name): void
    {
        $this->assertBaseJsonStruct(Post::class);
    }

    /**
     * @return Post
     */
    private function newPost(): Post
    {
        $post = new Post;
        $post->title = 'New post';
        $post->description = 'Lorem ipsum';
        $post->user_id = 1;
        $post->save();

        return $post;
    }
}
