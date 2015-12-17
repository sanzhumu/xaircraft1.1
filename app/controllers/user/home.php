<?php
use Xaircraft\DI;
use Xaircraft\Web\Mvc\Controller;

/**
 * @output_status_exception
 */
class user_home_controller extends Controller
{
    public function test_error()
    {
        throw new \Exception("test error");
    }
}