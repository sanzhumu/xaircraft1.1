<?php
use Xaircraft\DI;
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

    public function test_message()
    {
        $message1 = DI::get(Message::class);
        $message1->id = 2;
        $message2 = DI::get(Message::class);
        $message2->id = 3;

        var_dump($message1);
        var_dump($message2);
    }
}