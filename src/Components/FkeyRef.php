<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Components;

use Illuminate\Contracts\Support\Arrayable;

class FkeyRef implements \JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $foreign_table;

    /**
     * @var string
     */
    public string $references;

    /**
     * @param string $name
     * @param string $foreign_table
     * @param string $references
     */
    public function __construct(
        string $name,
        string $foreign_table,
        string $references
    ) {
        $this->name = $name;
        $this->foreign_table = $foreign_table;
        $this->references = $references;
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
