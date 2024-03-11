<?php
namespace app;

// 应用请求对象类
use Spatie\Macroable\Macroable;

class Request extends \think\Request
{
    use Macroable;
}
