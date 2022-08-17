<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Components;

use Illuminate\Contracts\Support\Arrayable;

class RelationRef implements \JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $type;

    /**
     * @var string
     */
    public string $parent;

    /**
     * @var string
     */
    public string $related;

    /**
     * @var array<string>
     */
    public array $keys;

    /**
     * @var string
     */
    public string $related_class_name;

    /**
     * @var string
     */
    public string $parent_class_name;

    /**
     * @param string $name
     * @param string $type
     * @param string $parent
     * @param string $related
     * @param array<string> $keys
     * @param string $related_class_name
     * @param string $parent_class_name
     */
    public function __construct(
        string $name,
        string $type,
        string $parent,
        string $related,
        array $keys,
        string $related_class_name,
        string $parent_class_name
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->parent = $parent;
        $this->related = $related;
        $this->keys = $keys;
        $this->related_class_name = $related_class_name;
        $this->parent_class_name = $parent_class_name;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'parent' => $this->parent,
            'related' => $this->related,
            'keys' => $this->keys,
            'related_class_name' => $this->related_class_name,
            'parent_class_name' => $this->parent_class_name,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
