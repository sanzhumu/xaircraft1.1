<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/15
 * Time: 19:47
 */

namespace Xaircraft\Database\Condition;


use Xaircraft\Database\QueryContext;

abstract class ConditionBuilder
{
    /**
     * @var QueryContext
     */
    protected $context;

    public function __construct(QueryContext $context)
    {
        $this->context = $context;
    }

    public abstract function getQueryString();
}