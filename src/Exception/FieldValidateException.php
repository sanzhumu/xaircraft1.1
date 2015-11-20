<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/20
 * Time: 14:44
 */

namespace Xaircraft\Exception;


use Xaircraft\Globals;

class FieldValidateException extends DataTableException
{
    private $field;

    public function __construct($table, $field, $message = "", $code = Globals::EXCEPTION_ERROR_DATABASE_INVALID_FIELD, \Exception $previous = null)
    {
        parent::__construct($table, $message, $code, $previous);

        $this->field = $field;
    }

    public function getField()
    {
        return $this->field;
    }
}