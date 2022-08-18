<?php

declare(strict_types=1);

namespace Shirokovnv\ModelReflection;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use ErrorException;
use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use Shirokovnv\ModelReflection\Components\FieldRef;
use Shirokovnv\ModelReflection\Components\FkeyRef;
use Shirokovnv\ModelReflection\Components\RelationRef;
use Shirokovnv\ModelReflection\Components\ScopeArgRef;
use Shirokovnv\ModelReflection\Components\ScopeRef;
use Shirokovnv\ModelReflection\Exceptions\DoctrineSchemaNotFoundException;
use Shirokovnv\ModelReflection\Exceptions\ReflectionException;

class ModelReflection
{
    /**
     * @var ConnectionInterface
     */
    private ConnectionInterface $conn;

    /**
     * @var AbstractSchemaManager
     */
    private AbstractSchemaManager $db_schema;

    /**
     * @var array<string, \Closure>
     */
    private array $rel_type_map;

    /**
     * @param ConnectionInterface $conn
     *
     * @throws \Exception
     */
    public function __construct(ConnectionInterface $conn)
    {
        $this->conn = $conn;

        $this->initDoctrineSchemaManager();
        $this->initRelationTypeMap();
    }

    /**
     * @throws \Exception
     *
     * @return void
     */
    private function initDoctrineSchemaManager(): void
    {
        if ($this->conn instanceof Connection) {
            $this->db_schema = $this->conn->getDoctrineSchemaManager();
        } else {
            throw new DoctrineSchemaNotFoundException();
        }
    }

    /**
     * @return void
     */
    private function initRelationTypeMap(): void
    {
        $this->rel_type_map = [
            'BelongsToMany' => function ($relation) {
                return [
                    'keys' => [
                        'relatedPivotKey' => $relation->getRelatedPivotKeyName(),
                        'foreignPivotKey' => $relation->getForeignPivotKeyName(),
                    ],
                ];
            },

            'BelongsTo' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'ownerKey' => $relation->getOwnerKeyName(),
                    ],
                ];
            },

            'HasOne' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                    ],
                ];
            },

            'HasMany' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                    ],
                ];
            },

            'MorphTo' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'morphType' => $relation->getMorphType(),
                    ],
                ];
            },

            'MorphOne' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                        'morphType' => $relation->getMorphType(),
                        'morphClass' => $relation->getMorphClass(),
                    ],
                ];
            },

            'MorphMany' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                        'morphType' => $relation->getMorphType(),
                        'morphClass' => $relation->getMorphClass(),
                    ],
                ];
            },

            'MorphToMany' => function ($relation) {
                return [
                    'keys' => [
                        'foreignKey' => $relation->getForeignPivotKeyName(),
                        'relatedKey' => $relation->getRelatedPivotKeyName(),
                        'morphType' => $relation->getMorphType(),
                        'morphClass' => $relation->getMorphClass(),
                    ],
                ];
            },

            'HasOneThrough' => function ($relation) {
                return [
                    'keys' => [
                        'firstKey' => $relation->getFirstKeyName(),
                        'secondKey' => $relation->getSecondLocalKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                        'foreignKey' => $relation->getForeignKeyName(),
                    ],
                ];
            },

            'HasManyThrough' => function ($relation) {
                return [
                    'keys' => [
                        'firstKey' => $relation->getFirstKeyName(),
                        'secondKey' => $relation->getSecondLocalKeyName(),
                        'localKey' => $relation->getLocalKeyName(),
                        'foreignKey' => $relation->getForeignKeyName(),
                    ],
                ];
            },

        ];
    }

    /**
     * @param string $model_class_name
     *
     * @throws \ReflectionException|ReflectionException
     *
     * @return ReflectedModel
     */
    public function reflect(string $model_class_name): ReflectedModel
    {
        /** @var Model $model */
        $model = new $model_class_name;

        $table_name = $model->getTable();

        $fields = $this->getModelFields($model);
        $relations = $this->getModelRelations($model_class_name);
        $foreign_keys = $this->getForeignKeys($table_name);
        $scopes = $this->getModelScopes($model_class_name);

        return new ReflectedModel(
            $model_class_name,
            $table_name,
            $fields,
            $relations,
            $foreign_keys,
            $scopes
        );
    }

    /**
     * @param Model $model
     *
     * @return Collection<FieldRef>
     */
    private function getModelFields(Model $model): Collection
    {
        $table_name = $model->getTable();
        $columns = $this->getColumns($table_name);
        $hidden = $model->getHidden();

        $fields = new Collection([]);

        foreach ($columns as $column) {
            $key = $column->getName();

            $fields->push(
                new FieldRef(
                    $key,
                    $this->getBaseType($column->getType()->getName()),
                    $column->getComment() ?? $key,
                    $model->isFillable($key),
                    $model->isGuarded($key),
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
     *
     * @throws \ReflectionException|ReflectionException
     *
     * @return Collection<RelationRef>
     */
    private function getModelRelations(string $model_class_name): Collection
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
                        throw new ReflectionException("Unknown relation type: $rel_type.");
                    }

                    $meta = $this->rel_type_map[$rel_type]($result);

                    $relation =
                        new RelationRef(
                            $method->getName(),
                            $rel_type,
                            (new ReflectionClass($result->getParent()))->getName(),
                            (new ReflectionClass($result->getRelated()))->getName(),
                            $meta['keys'],
                            get_class($result->getRelated()),
                            get_class($result->getParent())
                        );

                    $relations->push($relation);
                }
            } catch (ErrorException $e) {
            }
        }

        return $relations;
    }

    /**
     * @param string $table_name
     *
     * @return Collection<FkeyRef>
     */
    private function getForeignKeys(string $table_name): Collection
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
     * @param string $model_class_name
     *
     * @throws \ReflectionException|ReflectionException
     *
     * @return Collection<ScopeRef>
     */
    private function getModelScopes(string $model_class_name): Collection
    {
        $class_methods = new Collection(get_class_methods($model_class_name));

        /** @var Collection<string> $scope_method_names */
        $scope_method_names = $class_methods->filter(static function (string $method): bool {
            return Str::startsWith($method, 'scope');
        });

        $scope_collection = new Collection([]);

        foreach ($scope_method_names as $scope_method_name) {
            if (!class_exists($model_class_name)) {
                throw new ReflectionException("Class $model_class_name does not exist.");
            }

            $reflection = new ReflectionClass($model_class_name);
            $params = $reflection->getMethod($scope_method_name)->getParameters();
            $scope_name = Str::replaceFirst('scope', '', $scope_method_name);
            $scope_args = new Collection([]);

            foreach ($params as $param) {
                /** @phpstan-ignore-next-line */
                $reflected_type = $param->getType() ? $param->getType()->getName() : null;

                $scope_args->push(
                    new ScopeArgRef(
                        $param->getName(),
                        $param->getPosition(),
                        $param->isOptional(),
                        $reflected_type
                    )
                );
            }

            $scope_collection->push(
                new ScopeRef(
                    $scope_name,
                    $scope_args
                )
            );
        }

        return $scope_collection;
    }

    /**
     * @param string $table_name
     *
     * @return \Doctrine\DBAL\Schema\Column[]
     */
    private function getColumns(string $table_name): array
    {
        return $this->db_schema->listTableColumns($table_name);
    }

    /**
     * @param string $db_type
     *
     * @return string
     */
    private function getBaseType(string $db_type): string
    {
        switch ($db_type) {
            case 'bigint':
                return 'integer';
            default:
                return $db_type;
        }
    }
}
