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
    /**
     * @var string
     */
    public $related_class_name;
    /**
     * @var string
     */
    public $parent_class_name;

    public function __construct(string $name,
                                string $type,
                                string $parent,
                                string $related,
                                array $keys,
                                string $related_class_name,
                                string $parent_class_name
    )
    {

        $this->name = $name;
        $this->type = $type;
        $this->parent = $parent;
        $this->related = $related;
        $this->keys = $keys;
        $this->related_class_name = $related_class_name;
        $this->parent_class_name = $parent_class_name;

    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'parent' => $this->parent,
            'related' => $this->related,
            'keys' => $this->keys,
            'related_class_name' => $this->related_class_name,
            'parent_class_name' => $this->parent_class_name
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

}
