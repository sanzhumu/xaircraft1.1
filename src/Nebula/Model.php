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

    public function __construct()
    {
        if (isset($this->table)) {
            $table = $this->table;
        } else {
            $statements = explode('\\', get_called_class());
            $table = $statements[count($statements) - 1];
            $table = Strings::camelToSnake($table);
        }

        $this->initializeModel($table);
    }

    /**
     * @return bool|mixed
     */
    public function beforeSave()
    {
    }

    public function afterSave()
    {
    }
    /**
     * @return bool|mixed
     */
    public function beforeDelete()
    {
    }

    public function afterDelete($fields)
    {
    }
    /**
     * @return bool|mixed
     */
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

    public function isModified($field)
    {
        return $this->entity->isModified($field);
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
            if (false === $this->beforeSave()) return false;
            $result = $this->entity->save($fields);
            if ($result) $this->afterSave();

            return $result;
        });
    }

    public function delete()
    {
        return DB::transaction(function () {
            if (false === $this->beforeDelete()) return false;
            $key = $this->schema->getAutoIncrementField();
            $result = DB::table($this->schema->getName())
                ->where($key, $this->entity->$key)
                ->delete()->execute();
            if ($result) $this->afterDelete($this->fields());
            return $result;
        });
    }

    public function forceDelete()
    {
        return DB::transaction(function () {
            if (false === $this->beforeDelete()) return false;
            if (false === $this->beforeForceDelete()) return false;
            $key = $this->schema->getAutoIncrementField();
            $result = DB::table($this->schema->getName())
                ->where($key, $this->entity->$key)
                ->forceDelete()->execute();
            if ($result) $this->afterDelete($this->fields());
            if ($result) $this->afterForceDelete($this->fields());
            return $result;
        });
    }

    public function getSchema()
    {
        return $this->schema;
    }

    private function initializeModel($table)
    {
        $this->schema = DB::table($table)->getTableSchema();
        $this->entity = DB::entity($table);
        $this->table = $table;
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
        return $model;
    }

    public static function find($arg)
    {
        $model = self::model();
        if ($arg instanceof TableQuery) {
            $query = $arg;
        } else if (is_numeric($arg)) {
            $query = DB::table($model->schema->getName())
                ->where($model->schema->getAutoIncrementField(), $arg)
                ->select();
        } else {
            throw new ModelException("What do you want to find?");
        }

        $model->loadData($query);

        if (is_numeric($arg) && !$model->isExists()) {
            throw new ModelException("Record not exists.");
        }

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