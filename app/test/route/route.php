<?php
/**
 * 管理对应模块-路由文件
 * 管理路径：app\demo\controller
 * User Long
 * Date 2024/03/07
 */

use think\facade\Route;
use http\middleware\{AllowOriginMiddleware, AuthSettingConfigurationMiddleware};

/**
 * 示例路由
 * Route::post('create',  'demo.index/create'); // 新增路由
 * Route::delete('delete',  'demo.index/delete'); // 删除路由
 * Route::put('update',  'demo.index/update'); // 更新路由（全）
 * Route::patch('edit',  'demo.index/edit'); // 更新路由（局）
 * Route::get('pageQuery',  'demo.index/pageQuery'); // 查询路由(全)
 * Route::get('detail',  'demo.index/detail'); // 查询路由(局)
 */

// 路由
Route::group(function () {
    // 示例路由
    Route::group('test', function () {
        Route::get('list', 'demo.demo/pageQuery');// 分页查询路由
    });

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