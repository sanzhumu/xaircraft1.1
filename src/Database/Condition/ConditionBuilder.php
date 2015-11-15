<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:47
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\QueryContext;

interface ConditionBuilder
{
    public function __construct(QueryContext $context);

    public function getQueryString();
}