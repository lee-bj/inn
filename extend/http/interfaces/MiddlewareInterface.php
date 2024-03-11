<?php
/**
 * Desc: 接口中间件
 * User: lee
 * Date-Time: 2024/3/7/15:24
 */

namespace http\interfaces;

use app\Request;

interface MiddlewareInterface
{
    public function handle(Request $request, \Closure $next);
}