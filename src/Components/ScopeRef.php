<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Components;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class ScopeRef implements \JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var Collection<ScopeArgRef>
     */
    public Collection $args;

    /**
     * @param string $name
     * @param Collection<ScopeArgRef> $args
     */
    public function __construct(string $name, Collection $args)
    {
        $this->name = $name;
        $this->args = $args;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'args' => $this->args->map(
                static function (ScopeArgRef $arg): array {
                return $arg->toArray();
            }
            )->toArray(),
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
