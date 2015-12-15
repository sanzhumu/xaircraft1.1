# xaircraft1.1
PHP framework 1.1 for Xaircraft Co.

## Action 参数注入

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
