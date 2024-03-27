<?php
/**
 * Desc: redis
 * User: 黎笔家
 * Date-Time: 2024/3/11/15:59
 */

return [
    // 服务器地址
    'host'     => env('redis.host', '127.0.0.1'),
    // 端口
    'port'     => env('redis.port', 6379),
    //密码
    'password' => env('redis.password', '')
];