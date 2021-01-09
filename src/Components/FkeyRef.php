<?php


namespace Shirokovnv\ModelReflection\Components;


use Illuminate\Contracts\Support\Arrayable;

/**
 * Class FkeyRef
 * @package Shirokovnv\ModelReflection\Components
 */
class FkeyRef implements \JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $foreign_table;
    /**
     * @var string
     */
    public $references;

    public function __construct(string $name,
                                string $foreign_table,
                                string $references)
    {
        $this->name = $name;
        $this->foreign_table = $foreign_table;
        $this->references = $references;
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'foreign_table' => $this->foreign_table,
            'references' => $this->references,
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }


}
