<?php

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/12/7
 * Time: 18:05
 */
class LoginAuthorize2 implements \Xaircraft\Authentication\Contract\Authorize
{

    /**
     * @param \Xaircraft\Authentication\Contract\Credential $credential
     * @return bool
     */
    public function authorize(\Xaircraft\Authentication\Contract\Credential $credential)
    {
        return false;
    }
}