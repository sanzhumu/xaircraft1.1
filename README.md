# xaircraft1.1
PHP framework 1.1 for Xaircraft Co.

## 为什么要有这个框架？

WEB框架的意义在于：将良好的WEB开发过程、经验通过框架提供的便利、开发模式体现出来，使得使用框架的开发人员能够直接获得较好的体验，框架同时也会带来一些约束，这些约束是在取舍之中平衡的结果，通常是利大于弊的（受限于有限的知识面和认知层次），友好的约束（约定）通过固定的模式，带来了一些便利。

基于此，编写了这个PHP框架。

需要特别说明的是，该框架是在实际项目开发过程中创建、完善的，因此依然存在不少问题。

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
