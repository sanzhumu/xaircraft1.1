<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/6
 * Time: 21:16
 */

namespace Xaircraft\Authentication;


use Xaircraft\Authentication\Contract\CurrentUser;

class Auth
{
    public static function check()
    {
        /** @var CurrentUser $user */
        $user = AuthStorage::get();
        if (!isset($user) || 0 === $user->getID()) {
            return false;
        }
        return true;
    }

    public static function logout()
    {
        AuthStorage::clear();
    }

    public static function user()
    {
        /** @var CurrentUser $user */
        $user = AuthStorage::get();
        return $user;
    }
}