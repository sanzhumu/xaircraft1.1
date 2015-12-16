<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/16
 * Time: 17:03
 */

namespace Xaircraft\Core;


use Xaircraft\DI;
use Xaircraft\Nebula\Model;

class Json
{
    public static function toObject($json, $class)
    {
        if (is_string($class)) {
            $class = new \ReflectionClass($class);
        }

        $params = self::toArray($json);
        if (!empty($params)) {
            $object = DI::get($class->name);
            if ($object instanceof Model) {
                $object = $object->load($params);
            } else {
                $properties = $class->getProperties();
                if (!empty($properties)) {
                    foreach ($properties as $property) {
                        if (array_key_exists($property->name, $params)) {
                            $property->setValue($object, $params[$property->name]);
                        }
                    }
                }
            }
            return $object;
        }
        return null;
    }

    public static function toArray($json)
    {
        $list = json_decode($json, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Exception("Json encode error.[$json]");
        }
        return $list;
    }
}