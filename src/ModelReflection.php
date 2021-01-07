<?php

namespace Shirokovnv\ModelReflection;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Shirokovnv\ModelReflection\Components\FieldRef;
use Shirokovnv\ModelReflection\Components\FkeyRef;
use Shirokovnv\ModelReflection\Components\RelationRef;
use Shirokovnv\ModelReflection\Exceptions\UnknownRelTypeException;
use ReflectionClass;
use ReflectionMethod;
use ErrorException;

/**
 * Class ModelReflection
 * @package Shirokovnv\ModelReflection
 */
class ModelReflection
{
    /**
     * @var Illuminate\Database\Connection
     */
    private $conn;
    /**
     * @var
     */
    private $db_schema;
    /**
     * @var array
     */
    private $rel_type_map;

    /**
     * ModelReflection constructor.
     * @param $conn
     */
    function __construct($conn)
    {
        $this->conn = $conn;

        $this->db_schema = $this->conn->getDoctrineSchemaManager();
        $this->initRelationTypeMap();
    }

    private function initRelationTypeMap()
    {
        $this->rel_type_map = [
            'BelongsToMany' => function ($relation) {
                return [
                    'keys' => [
                        'relatedPivotKey' => $relation->getRelatedPivotKeyName(),
                        'foreignPivotKey' => $relation->getForeignPivotKeyName()
                    ]
                ];
            },

            'BelongsTo' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'ownerKey' => $relation->getOwnerKeyName()
                    ]
                ];
            },

            'HasOne' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'localKey' => $relation->getLocalKeyName()
                    ]
                ];
            },

            'HasMany' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'localKey' => $relation->getLocalKeyName()
                    ]
                ];
            },

            'MorphTo' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'morphType' => $relation->getMorphType()
                    ]
                ];
            },

            'MorphOne' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                        'morphType' => $relation->getMorphType(),
                        'morphClass' => $relation->getMorphClass()
                    ]
                ];
            },

            'MorphMany' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                        'morphType' => $relation->getMorphType(),
                        'morphClass' => $relation->getMorphClass()
                    ]
                ];
            },

            'MorphToMany' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignPivotKeyName(),
                        'relatedKey' => $relation->getRelatedPivotKeyName(),
                        'morphType' => $relation->getMorphType(),
                        'morphClass' => $relation->getMorphClass()
                    ]
                ];
            },

            'HasOneThrough' => function ($relation) {
                return [
                    'keys' => [
                        'firstKey' => $relation->getFirstKeyName(),
                        'secondKey' => $relation->getSecondLocalKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                        'foreignKey' => $relation->getForeignKeyName()
                    ]
                ];
            },

            'HasManyThrough' => function ($relation) {
                return [
                    'keys' => [
                        'firstKey' => $relation->getFirstKeyName(),
                        'secondKey' => $relation->getSecondLocalKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                        'foreignKey' => $relation->getForeignKeyName()
                    ]
                ];
            }

        ];
    }

    public function make(string $model_class_name)
    {
        $table_name = $this->getModelTable($model_class_name);

        $fields = $this->getModelFields($model_class_name);
        $relations = $this->getModelRelations($model_class_name);
        $foreign_keys = $this->getForeignKeys($table_name);

        return new ReflectedModel(
            $model_class_name,
            $table_name,
            $fields,
            $relations,
            $foreign_keys
        );
    }

    /**
     * Get the information about model representation in the database
     * @param string $model_class_name
     * @return array
     * @throws UnknownRelTypeException
     */
    public function getModelSchema(string $model_class_name)
    {
        $reflection = $this->make($model_class_name);
        return $reflection->toArray();
    }

    /**
     * @param string $model_class_name
     * @return Collection
     */
    private function getModelFields(string $model_class_name)
    {

        $table_name = $this->getModelTable($model_class_name);
        $columns = $this->getColumns($table_name);
        $hidden = $this->getModelHidden($model_class_name);

        $fields = new Collection([]);

        foreach ($columns as $column) {
            $key = $column->getName();

            $fields->push(
                new FieldRef(
                    $key,
                    $this->getBaseType($column->getType()->getName()),
                    $column->getComment() ?? $key,
                    with(new $model_class_name)->isFillable($key),
                    with(new $model_class_name)->isGuarded($key),
                    (in_array($key, $hidden)),
                    $column->getNotnull(),
                    $column->getDefault()
                )
            );
        }

        return $fields;
    }

    /**
     * @param string $model_class_name
     * @return Collection
     * @throws UnknownRelTypeException
     * @throws \ReflectionException
     */
    private function getModelRelations(string $model_class_name)
    {

        $model = new $model_class_name;

        $relations = new Collection([]);

        $methods = (new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach ($methods as $method) {
            if ($method->class != get_class($model) ||
                !empty($method->getParameters()) ||
                $method->getName() == __FUNCTION__) {
                continue;
            }

            try {
                $result = $method->invoke($model);

                if ($result instanceof Relation) {

                    $rel_type = (new ReflectionClass($result))->getShortName();
                    if (!array_key_exists($rel_type, $this->rel_type_map)) {
                        throw new UnknownRelTypeException($rel_type);
                    }

                    $meta = $this->rel_type_map[$rel_type]($result);

                    $relation =
                        new RelationRef(
                            $method->getName(),
                            $rel_type,
                            (new ReflectionClass($result->getParent()))->getName(),
                            (new ReflectionClass($result->getRelated()))->getName(),
                            $meta['keys']
                        );

                    $relations->push($relation);
                }
            } catch (ErrorException $e) {
            }
        }

        return $relations;
    }

    /**
     * @param string $model_class_name
     * @return mixed
     */
    private function getModelTable(string $model_class_name)
    {
        return with(new $model_class_name)->getTable();
    }

    /**
     * @param $model_class_name
     * @return mixed
     */
    private function getModelHidden($model_class_name)
    {
        return with(new $model_class_name)->getHidden();
    }

    /**
     * @param string $table_name
     * @return Collection
     */
    private function getForeignKeys(string $table_name)
    {
        $foreign_keys = $this->db_schema->listTableForeignKeys($table_name);

        $result = new Collection([]);
        foreach ($foreign_keys as $fkey) {

            $key_name = $fkey->getColumns()[0];
            $result->push(
                new FkeyRef(
                    $key_name,
                    $fkey->getForeignTableName(),
                    $fkey->getForeignColumns()[0]
                )
            );

        }

        return $result;
    }

    /**
     * @param string $table_name
     * @return mixed
     */
    private function getColumns(string $table_name)
    {
        return $this->db_schema->listTableColumns($table_name);
    }

    /**
     * @param string $db_type
     * @return string
     */
    private function getBaseType(string $db_type)
    {
        switch ($db_type) {
            case 'bigint':
                return 'integer';
                break;
            default:
                return $db_type;
                break;
        }
    }

}
