<?php


namespace Shirokovnv\ModelReflection\Components;


use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class ScopeRef
 * @package Shirokovnv\ModelReflection\Components
 */
class ScopeRef implements \JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var Collection
     */
    public $args;

    public function __construct(string $name, Collection $args) {
        $this->name = $name;
        $this->args = $args;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'args' => $this->args->map(function ($arg) { return $arg->toArray(); })->toArray()
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
