<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/14
 * Time: 21:06
 */

namespace Xaircraft\Database;


class ColumnInfo
{
    public $name;

    public $type;

    public $default;

    public $length;

    public $comment;

    public $nullable = false;

    public $primary = false;

    public $autoIncrement = false;

    public $table;

    public $dateType;

    public $charMaxLength;

    public $charOctetLength;

    public $numericPrecision;

    public $numericScale;

    public $numericUnsigned;

    public $dateTimePrecision;

    public $charSetName;

    public $collationName;
}