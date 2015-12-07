<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/21
 * Time: 22:08
 */

namespace Xaircraft\Nebula;


use Xaircraft\Core\Container;
use Xaircraft\Core\Strings;
use Xaircraft\Database\TableQuery;
use Xaircraft\Database\TableSchema;
use Xaircraft\DB;
use Xaircraft\DI;
use Xaircraft\Exception\ModelException;

abstract class Model extends Container
{
    /**
     * @var TableSchema
     */
    private $schema;

    /**
     * @var Entity
     */
    private $entity;

    protected $table;

    public function beforeSave()
    {
    }

    public function afterSave()
    {
    }

    public function beforeDelete()
    {
    }

    public function afterDelete($fields)
    {
    }

    public function beforeForceDelete()
    {
    }

    public function afterForceDelete($fields)
    {
    }

    public function isExists()
    {
        return $this->entity->isExists();
    }

    public function fields()
    {
        return $this->entity->fields();
    }

    public function save(array $fields = null)
    {
        if (!empty($fields)) {
            foreach ($fields as $key => $value) {
                $this->$key = $value;
            }
        }

        return DB::transaction(function () use ($fields) {
            $this->beforeSave();
            $result = $this->entity->save($fields);
            $this->afterSave();

            return $result;
        });
    }

    public function delete()
    {
        return DB::transaction(function () {
            $this->beforeDelete();
            $key = $this->schema->getAutoIncrementField();
            $result = DB::table($this->schema->getTableName())
                ->where($key, $this->entity->$key)
                ->delete()->execute();
            $this->afterDelete($this->fields());
            return $result;
        });
    }

    public function forceDelete()
    {
        return DB::transaction(function () {
            $this->beforeForceDelete();
            $key = $this->schema->getAutoIncrementField();
            $result = DB::table($this->schema->getTableName())
                ->where($key, $this->entity->$key)
                ->forceDelete()->execute();
            $this->afterForceDelete($this->fields());
            return $result;
        });
    }

    private function initializeModel($table)
    {
        $this->schema = DB::table($table)->getTableSchema();
        $this->entity = DB::entity($table);
    }

    private function loadData(TableQuery $query)
    {
        $this->entity = DB::entity($query);
    }

    /**
     * @return Model
     */
    public static function model()
    {
        $table = get_called_class();
        /**
         * @var Model $model
         */
        $model = DI::get($table);

        if (isset($model->table)) {
            $table = $model->table;
        } else {
            $statements = explode('\\', $table);
            $table = $statements[count($statements) - 1];
            $table = Strings::camelToSnake($table);
        }

        $model->initializeModel($table);
        return $model;
    }

    public static function find($arg)
    {
        $model = self::model();
        if ($arg instanceof TableQuery) {
            $query = $arg;
        } else if (is_numeric($arg)) {
            $query = DB::table($model->schema->getTableName())
                ->where($model->schema->getAutoIncrementField(), $arg)
                ->select();
        } else {
            throw new ModelException("What do you want to find? ");
        }

        $model->loadData($query);

        return $model;
    }

    public function __get($field)
    {
        return $this->entity->$field;
    }

    public function __set($field, $value)
    {
        $this->entity->$field = $value;
    }
}