<?php
/**
 * 管理对应模块-路由文件
 * 管理路径：app\test\controller
 * User Long
 * Date 2024/03/07
 */

use think\facade\Route;
use http\middleware\{AllowOriginMiddleware, AuthSettingConfigurationMiddleware};

/**
 * 示例路由
 * Route::post('create',  'test.index/create'); // 新增路由
 * Route::delete('delete',  'test.index/delete'); // 删除路由
 * Route::put('update',  'test.index/update'); // 更新路由（全）
 * Route::patch('edit',  'test.index/edit'); // 更新路由（局）
 * Route::get('pageQuery',  'test.index/pageQuery'); // 查询路由(全)
 * Route::get('detail',  'test.index/detail'); // 查询路由(局)
 */

// 需登录授权路由
Route::group(function () {

  })->middleware(AuthSettingConfigurationMiddleware::class)
    ->middleware(AllowOriginMiddleware::class);

// 无需登录授权路由
Route::group(function () {

  })->middleware(AuthSettingConfigurationMiddleware::class)
    ->middleware(AllowOriginMiddleware::class);

Route::miss(function() {
    if(app()->request->isOptions())
        return \think\Response::create('ok')->code(200)->header([
            'Access-Control-Allow-Origin'   => '*',
            'Access-Control-Allow-Headers'  => 'Authori-zation,Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With',
            'Access-Control-Allow-Methods'  => 'GET,POST,PATCH,PUT,DELETE,OPTIONS,DELETE',
        ]);
    else
        return \think\Response::create()->code(404);
});