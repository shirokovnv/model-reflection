<?php


namespace Shirokovnv\ModelReflection\Components;


use Shirokovnv\ModelReflection\Interfaces\Arrayable;

/**
 * Class FieldRef
 * @package Shirokovnv\ModelReflection\Components
 */
class FieldRef implements \JsonSerializable, Arrayable
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
    public $label;
    /**
     * @var bool
     */
    public $fillable;
    /**
     * @var bool
     */
    public $guarded;
    /**
     * @var bool
     */
    public $hidden;
    /**
     * @var bool
     */
    public $required;
    /**
     * @var mixed
     */
    public $default;

    public function __construct(
        string $name,
        string $type,
        string $label,
        bool $fillable,
        bool $guarded,
        bool $hidden,
        bool $required,
        $default
    )
    {
        $this->name = $name;
        $this->type = $type;
        $this->label = $label;
        $this->fillable = $fillable;
        $this->guarded = $guarded;
        $this->hidden = $hidden;
        $this->required = $required;
        $this->default = $default;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'label' => $this->label,
            'fillable' => $this->fillable,
            'guarded' => $this->guarded,
            'hidden' => $this->hidden,
            'required' => $this->required,
            'default' => $this->default
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
