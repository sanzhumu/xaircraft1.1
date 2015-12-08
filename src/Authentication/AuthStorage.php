<?php
/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/7
 * Time: 17:23
 */

namespace Xaircraft\Authentication;


use Xaircraft\Authentication\Contract\CurrentUser;
use Xaircraft\Web\Session;

class AuthStorage
{
    private static $currentUserSessionID = '__current_user__';

    public static function set(CurrentUser $user)
    {
        Session::put(self::$currentUserSessionID, $user);
    }

    public static function get()
    {
        return Session::get(self::$currentUserSessionID);
    }

    public static function clear()
    {
        Session::forget(self::$currentUserSessionID);
    }
}