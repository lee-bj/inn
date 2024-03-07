<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'app'             => \command\App::class // 生成应用层
        , 'table'         => \command\Table::class // 生成数据表
        , 'module'        => \command\Module::class // 生成模块层
        , 'timer'         => \command\Timer::class // 定时任务
    ],
];
