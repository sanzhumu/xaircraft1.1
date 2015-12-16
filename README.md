# xaircraft1.1
PHP framework 1.1 for Xaircraft Co.

## 1. Action 参数注入

支持 Action 参数注入，示例如下：

```PHP
class home_controller extends Controller
{
    /**
     * @param $index
     */
     public function index($index = 0)
     {
        var_dump($index);
     }
}
```
若请求 URL：http://localhost/?index=1

则页面会输出：

```PHP
1
```

支持定义数组类型的参数，示例如下：

```PHP
class home_controller extends Controller
{
    /**
     * @param $ids
     */
     public function index(array $ids)
     {
        var_dump($ids);
     }
}
```
若请求 URL：http://localhost/?ids=[1,2,3,4,5,6]

则页面会输出：

```PHP
array (size=6)
  0 => int 1
  1 => int 2
  2 => int 3
  3 => int 4
  4 => int 5
  5 => int 6
```
此时若请求 URL：http://localhost/
会抛出参数不能为空的异常。

支持参数的可空设置，示例如下：
```PHP
class home_controller extends Controller
{
    /**
     * @param array $ids
     */
     public function index(array $ids = null)
     {
        var_dump($ids);
     }
}
```
若请求 URL：http://localhost/

则页面会输出：

```PHP
null
```

支持定义参数的类型，示例如下：

定义一个类：Message
```PHP
class Message
{
    public $id;

    public $content;
}
```
可以在 Action 参数中指定 Message 类型：
```PHP
class home_controller extends Controller
{
    /**
     * @param Message $message
     */
     public function index(Message $message)
     {
        var_dump($message);
     }
}
```

若请求 URL：http://localhost/?message={"id":1,"content":"这是内容"}

则页面会输出：
```PHP
object(Message)[52]
  public 'id' => int 1
  public 'content' => string '这是内容' (length=4)
```

对于 POST 提交的数据字段，需要在 Action 的 DocComment 中对应参数声明加上 POST 关键字，示例如下：
```PHP
class home_controller extends Controller
{
    /**
     * @param Message $message POST
     */
     public function index(Message $message)
     {
        var_dump($message);
     }
}
```

## 2. Json 工具类

支持将JSON字符串转换成指定的对象，示例如下：

定义一个类：Message
```PHP
class Message
{
    public $id;

    public $content;
}
```

进行JSON字符串解析并自动转换成Message对象：
```PHP
$message = Json::toObject('{"id":12,"content":"hello"}', Message::class);
var_dump($message);
```

执行结果：
```PHP
object(Message)[37]
  public 'id' => int 12
  public 'content' => string 'hello' (length=5)
```

支持将JSON字符串转换成数组，示例如下：

```PHP
$list = Json::toArray("[1,2,3,4,5,6]");
var_dump($list);
```

执行结果：
```PHP
array (size=6)
  0 => int 1
  1 => int 2
  2 => int 3
  3 => int 4
  4 => int 5
  5 => int 6
```

## 3. 依赖注入和控制反转

通过DI工具类，可以实现依赖注入和控制反转。

### 示例1. 依赖注入

一个 Controller，依赖 Message 类，在访问该 Controller 时，框架会自动创建 Message 对象并注入。

定义一个 Message 类：
```PHP
class Message
{
    public $id = 1;

    public $content = "Hello message.";
}
```

定义一个 Controller：
```PHP
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
    }
}
```

执行结果：
```PHP
object(Message)[30]
  public 'id' => int 0
  public 'content' => string 'Hello message.' (length=14)
```