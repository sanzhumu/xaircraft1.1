<?php
use Xaircraft\Web\Mvc\Controller;

/**
 * Created by PhpStorm.
 * User: lbob
 * Date: 2015/11/12
 * Time: 17:44
 */
class user_home_controller extends Controller
{
    /**
     * @var Message
     */
    private $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function index()
    {
        var_dump($this->message);
        $this->message->sendEmail("Bob");
    }
}