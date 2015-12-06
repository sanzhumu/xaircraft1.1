<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/6
 * Time: 23:44
 */

namespace Xaircraft\Web\Mvc;


use Xaircraft\DI;
use Xaircraft\Exception\HttpAuthenticationException;

class AuthorizeHelper
{
    private static $authorized = array();

    public static function authorize($comment)
    {
        if (preg_match('#@auth[ ]+(?<authorize>[a-zA-Z][a-zA-Z0-9\_\\\]+)#i', $comment, $matches)) {
            $key = $matches['authorize'];
            if (array_key_exists($key, self::$authorized)) {
                return true;
            }
            try {
                /** @var \Xaircraft\Authentication\Contract\Authorize $authorize */
                $authorize = DI::get($key);
                if (!$authorize->authorize(DI::get(HttpAuthCredential::class))) {
                    throw new HttpAuthenticationException("Http authorize failure.");
                }
            } catch (\Exception $ex) {
                throw new HttpAuthenticationException($ex->getMessage(), $ex->getCode(), $ex);
            }
            self::$authorized[$key] = true;
        }
    }

    public static function authorizeController($controller)
    {
        $ref = new \ReflectionClass($controller);
        self::authorize($ref->getDocComment());
    }
}