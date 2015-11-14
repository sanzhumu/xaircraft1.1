<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/14
 * Time: 22:13
 */

namespace Xaircraft\Exception;


use Xaircraft\Globals;

class DataTableException extends DatabaseException
{
    private $table;

    public function __construct($table, $message = "", $code = Globals::EXCEPTION_ERROR_DATABASE, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->table = $table;
    }

    public function getTable()
    {
        return $this->table;
    }
}