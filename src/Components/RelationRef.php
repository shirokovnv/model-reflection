<?php

namespace Shirokovnv\ModelReflection\Components;


use Illuminate\Contracts\Support\Arrayable;

/**
 * Class RelationRef
 * @package Shirokovnv\ModelReflection\Components
 */
class RelationRef implements \JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $parent;
    /**
     * @var string
     */
    public $related;
    /**
     * @var array
     */
    public $keys;

    public function __construct(string $name,
                                string $type,
                                string $parent,
                                string $related,
                                array $keys
    )
    {

        $this->name = $name;
        $this->type = $type;
        $this->parent = $parent;
        $this->related = $related;
        $this->keys = $keys;

    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'parent' => $this->parent,
            'related' => $this->related,
            'keys' => $this->keys,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

}
