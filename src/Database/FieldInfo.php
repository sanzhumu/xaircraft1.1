<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 15:31
 */

namespace Xaircraft\Database;


use Xaircraft\Database\Func\FieldFunction;
use Xaircraft\Exception\QueryException;

class FieldInfo
{
    private $name;

    private $alias;

    private $field;

    private $prefix;

    /**
     * @var FieldFunction
     */
    private $func;

    public $queryHandler;

    public static function make($name, $alias = null, $queryHandler = null)
    {
        $field = new FieldInfo();
        if ($name instanceof FieldFunction) {
            $field->func = $name;
            $field->field = $name->field;
        } else {
            $field->name = trim($name);
        }
        $field->alias = isset($alias) ? trim($alias) : null;
        $field->queryHandler = $queryHandler;

        $field->parseName();

        return $field;
    }

    private function parseName()
    {
        if (isset($this->name)) {
            if (preg_match('#^[a-zA-Z][a-zA-Z0-9\_\-]*$#i', $this->name)) {
                $this->field = $this->name;
            }
            if (preg_match('#^(?<prefix>[a-zA-Z][a-zA-Z0-9\_\-]*)\.(?<field>[a-zA-Z][a-zA-Z0-9\_\-]*)$#i', $this->name, $match)) {
                $this->prefix = $match['prefix'];
                $this->field = $match['field'];
            }
            if (preg_match('#[ ]+[aA][sS][ ]+#i', $this->name, $match)) {
                throw new QueryException("Field's alias pls use array('{{field}}' => '{{alias}}')");
            }
        }
    }

    public function getName(QueryContext $context)
    {
        if (isset($this->func)) {
            $field = $this->func->getString($context);
        } else {
            $field = $context->getField($this->field, $this->prefix);
        }
        if (isset($this->alias)) {
            $field = "$field AS $this->alias";
        }
        return $field;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }
}