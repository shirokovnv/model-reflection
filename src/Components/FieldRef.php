<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection\Components;

use Illuminate\Contracts\Support\Arrayable;

class FieldRef implements \JsonSerializable, Arrayable
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
    public string $label;

    /**
     * @var bool
     */
    public bool $fillable;

    /**
     * @var bool
     */
    public bool $guarded;

    /**
     * @var bool
     */
    public bool $hidden;

    /**
     * @var bool
     */
    public bool $required;

    /**
     * @var string|null
     */
    public ?string $default;

    /**
     * @param string      $name
     * @param string      $type
     * @param string      $label
     * @param bool        $fillable
     * @param bool        $guarded
     * @param bool        $hidden
     * @param bool        $required
     * @param string|null $default
     */
    public function __construct(
        string $name,
        string $type,
        string $label,
        bool $fillable,
        bool $guarded,
        bool $hidden,
        bool $required,
        ?string $default
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->fillable = $fillable;
        $this->guarded = $guarded;
        $this->hidden = $hidden;
        $this->required = $required;
        $this->default = $default;
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
