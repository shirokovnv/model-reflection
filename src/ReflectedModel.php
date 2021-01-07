<?php


namespace Shirokovnv\ModelReflection;


use Shirokovnv\ModelReflection\Interfaces\Arrayable;
use Illuminate\Support\Collection;

class ReflectedModel implements \JsonSerializable, Arrayable
{
    public $name;
    public $table_name;
    public $fields;
    public $relations;
    public $foreign_keys;

    public function __construct(
        string $name,
        string $table_name,
        Collection $fields,
        Collection $relations,
        Collection $foreign_keys
    )
    {
        $this->name = $name;
        $this->table_name = $table_name;
        $this->fields = $fields;
        $this->relations = $relations;
        $this->foreign_keys = $foreign_keys;
    }

    public function toArray()
    {

        return [
            'name' => $this->name,

            'table_name' => $this->table_name,

            'fields' => $this->fields->map(function ($field) {
                return $field->toArray();
            })->toArray(),

            'relations' => $this->relations->map(function ($rel) {
                return $rel->toArray();
            })->toArray(),

            'foreign_keys' => $this->foreign_keys->map(function ($fkey) {
                return $fkey->toArray();
            })->toArray()
        ];

    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
