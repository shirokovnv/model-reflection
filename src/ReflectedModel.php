<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;
use Shirokovnv\ModelReflection\Components\FieldRef;
use Shirokovnv\ModelReflection\Components\FkeyRef;
use Shirokovnv\ModelReflection\Components\RelationRef;
use Shirokovnv\ModelReflection\Components\ScopeRef;

class ReflectedModel implements \JsonSerializable, Arrayable
{
    /**
     * @var string
     */
    public string $name;

    /**
     * @var string
     */
    public string $table_name;

    /**
     * @var Collection<FieldRef>
     */
    public Collection $fields;

    /**
     * @var Collection<RelationRef>
     */
    public Collection $relations;

    /**
     * @var Collection<FkeyRef>
     */
    public Collection $foreign_keys;

    /**
     * @var Collection<ScopeRef>
     */
    public Collection $scopes;

    /**
     * @param string                  $name
     * @param string                  $table_name
     * @param Collection<FieldRef>    $fields
     * @param Collection<RelationRef> $relations
     * @param Collection<FkeyRef>     $foreign_keys
     * @param Collection<ScopeRef>    $scopes
     */
    public function __construct(
        string $name,
        string $table_name,
        Collection $fields,
        Collection $relations,
        Collection $foreign_keys,
        Collection $scopes
    ) {
        $this->name = $name;
        $this->table_name = $table_name;
        $this->fields = $fields;
        $this->relations = $relations;
        $this->foreign_keys = $foreign_keys;
        $this->scopes = $scopes;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'table_name' => $this->table_name,
            'fields' => $this->fields->map(static function (FieldRef $field): array {
                return $field->toArray();
            })->toArray(),
            'relations' => $this->relations->map(static function (RelationRef $rel): array {
                return $rel->toArray();
            })->toArray(),
            'foreign_keys' => $this->foreign_keys->map(static function (FkeyRef $fkey): array {
                return $fkey->toArray();
            })->toArray(),
            'scopes' => $this->scopes->map(static function (ScopeRef $scope): array {
                return $scope->toArray();
            })->toArray(),
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
