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
    private $name;

    private $type;

    private $default;

    private $length;

    private $comment;

    private $nullable = false;

    private $primary = false;

    private $autoIncrement = false;

    private $table;

    private $dateType;

    private $charMaxLength;

    private $charOctetLength;

    private $numericPrecision;

    private $numericScale;

    private $numericUnsigned;

    private $dateTimePrecision;

    private $charSetName;

    private $collationName;
}