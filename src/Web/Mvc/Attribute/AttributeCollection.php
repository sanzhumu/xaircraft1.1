<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/8
 * Time: 5:35
 */

namespace Xaircraft\Web\Mvc\Attribute;


use Xaircraft\Core\Container;

class AttributeCollection
{
    private $attributes;

    private function __construct() {}

    public static function create($comment)
    {
        $attributes = AttributeInfo::create($comment);

        if (isset($attributes)) {
            $collection = new AttributeCollection();
            foreach ($attributes as $info) {
                $attribute = Attribute::createFromAttributeInfo($info);
                if (isset($attribute)) {
                    $collection->attributes[] = $attribute;
                }
            }
            return $collection;
        }
        return null;
    }

    public function invoke()
    {
        foreach ($this->attributes as $attribute) {
            /** @var Attribute $attribute */
            $attribute->invoke();
        }
    }

    public function exists($class)
    {
        foreach ($this->attributes as $attribute) {
            if ($class === get_class($attribute)) {
                return true;
            }
        }
        return false;
    }
}