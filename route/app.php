<?php
/**
 * 管理对应模块-路由文件
 * 管理路径：app\center\controller
 * User Long
 * Date 2023/06/27
 */

use think\facade\Route;

Route::get('/', function () {
    return 'hello,word!';
});