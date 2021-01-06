<?php

namespace Shirokovnv\ModelReflection;

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
    function __construct($conn) {
        $this->conn = $conn;

        $this->db_schema = $this->conn->getDoctrineSchemaManager();
        $this->initRelationTypeMap();
    }

    private function initRelationTypeMap() {
        $this->rel_type_map = [
            'BelongsToMany' => function($relation) {
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
            }

        ];
    }

    /**
     * Get the information about model representation in the database
     * @param string $model_class_name
     * @return array
     * @throws UnknownRelTypeException
     */
    public function getModelSchema(string $model_class_name)
    {
        $table_name = $this->getModelTable($model_class_name);

        $columns = $this->getColumns($table_name);
        $relations = $this->getModelRelations($model_class_name);
        $hidden = $this->getModelHidden($model_class_name);
        $foreign_keys = $this->getForeignKeys($table_name);

        $json_schema = [];
        $fields = [];
        foreach ($columns as $column)
        {
            $key = $column->getName();

            $fields[$key] = [
                'type' => $this->getBaseType($column->getType()->getName()),
                'label' => $column->getComment() ?? $key,
                'fillable' => with(new $model_class_name)->isFillable($key),
                'guarded' => with(new $model_class_name)->isGuarded($key),
                'hidden' => (in_array($key, $hidden)),
                'required' => $column->getNotnull(),
                'default' => $column->getDefault()
            ];

        }
        $json_schema['name'] = $model_class_name;
        $json_schema['table_name'] = $table_name;
        $json_schema['relations'] = $relations;
        $json_schema['fields'] = $fields;
        $json_schema['foreign_keys'] = $foreign_keys;

        return $json_schema;
    }

    /**
     * @param string $model_class_name
     * @return array
     * @throws UnknownRelTypeException
     * @throws \ReflectionException
     */
    private function getModelRelations(string $model_class_name) {

        $model = new $model_class_name;

        $relations = [];

        $methods = (new ReflectionClass($model))->getMethods(ReflectionMethod::IS_PUBLIC);

        foreach($methods as $method)
        {
            if ($method->class != get_class($model) ||
                !empty($method->getParameters()) ||
                $method->getName() == __FUNCTION__) {
                continue;
            }

            try {
                $result = $method->invoke($model);
                $rel_type = config('model-reflection.base_eloquent_rel_type');

                if ($result instanceof $rel_type) {
                    $relation = [
                        'type' => (new ReflectionClass($result))->getShortName(),
                        'model' => (new ReflectionClass($result->getRelated()))->getName()
                    ];

                    if (!array_key_exists($relation['type'], $this->rel_type_map)) {
                        throw new UnknownRelTypeException($relation['type']);
                    }

                    $meta = $this->rel_type_map[$relation['type']]($result);
                    $relation['keys'] = $meta['keys'];

                    $relations[$method->getName()] = $relation;
                }
            } catch(ErrorException $e) {}
        }

        return $relations;
    }

    /**
     * @param string $model_class_name
     * @return mixed
     */
    private function getModelTable(string $model_class_name) {
        return with(new $model_class_name)->getTable();
    }

    /**
     * @param $model_class_name
     * @return mixed
     */
    private function getModelHidden($model_class_name) {
        return with(new $model_class_name)->getHidden();
    }

    /**
     * @param string $table_name
     * @return array
     */
    private function getForeignKeys(string $table_name) {
        $foreign_keys = $this->db_schema->listTableForeignKeys($table_name);

        $result = [];
        foreach ($foreign_keys as $fkey) {

            $key_name = $fkey->getColumns()[0];
            $result[$key_name] =
                [
                    'foreign_table' => $fkey->getForeignTableName(),
                    'references' => $fkey->getForeignColumns()[0]
                ];

        }

        return $result;
    }

    /**
     * @param string $table_name
     * @return mixed
     */
    private function getColumns(string $table_name) {
        return $this->db_schema->listTableColumns($table_name);
    }

    /**
     * @param string $db_type
     * @return string
     */
    private function getBaseType(string $db_type)
    {
        switch ($db_type)
        {
            case 'bigint': return 'integer'; break;
            default: return $db_type; break;
        }
    }

}
