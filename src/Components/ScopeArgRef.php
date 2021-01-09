<?php


namespace Shirokovnv\ModelReflection\Components;


use Illuminate\Contracts\Support\Arrayable;

/**
 * Class ScopeArgRef
 * @package Shirokovnv\ModelReflection\Components
 */
class ScopeArgRef implements \JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var int
     */
    public $position;
    /**
     * @var mixed|null
     */
    public $typeHint;

    /**
     * @var bool
     */
    public $isOptional;

    public function __construct(string $name, int $position, $isOptional, $typeHint = null) {
        $this->name = $name;
        $this->position = $position;
        $this->isOptional = $isOptional;
        $this->typeHint = $typeHint;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'isOptional' => $this->isOptional,
            'position' => $this->position,
            'typeHint' => $this->typeHint
        ];
    }

    public function jsonSerialize()
    {
        $this->toArray();
    }
}
