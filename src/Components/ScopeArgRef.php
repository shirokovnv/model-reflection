<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Components;

use Illuminate\Contracts\Support\Arrayable;

class ScopeArgRef implements \JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var int
     */
    public int $position;

    /**
     * @var mixed|null
     */
    public $typeHint;

    /**
     * @var bool
     */
    public bool $is_optional;

    /**
     * @param string     $name
     * @param int        $position
     * @param bool       $is_optional
     * @param mixed|null $typeHint
     */
    public function __construct(string $name, int $position, bool $is_optional, $typeHint = null)
    {
        $this->name = $name;
        $this->position = $position;
        $this->is_optional = $is_optional;
        $this->typeHint = $typeHint;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return (array) $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
