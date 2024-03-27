<?php
/**
 * Desc 示例 - 逻辑层
 * User
 * Date 2023/07/11
 */

declare (strict_types=1);

namespace app\test\logic\demo;

use basic\BaseLogic;

class DemoLogic extends BaseLogic
{
    // 新增
    public static function create()
    {
        return app('json')->success('您好！这是一个[create]示例应用');
    }

    // 删除
    public static function delete()
    {
        return app('json')->success('您好！这是一个[delete]示例应用');
    }

    // 修改（全）
    public static function update()
    {
        return app('json')->success('您好！这是一个[update]示例应用');
    }

    // 修改（局）
    public static function edit()
    {
        return app('json')->success('您好！这是一个[edit]示例应用');
    }

    // 详情
    public static function detail()
    {
        return app('json')->success('您好！这是一个[detail]示例应用');
    }

    // 分页查询
    public static function pageQuery()
    {
        return app('json')->success('您好！这是一个[pageQuery]示例应用');
    }

    // 列表(无分页)
    public static function listQuery()
    {
        return app('json')->success('您好！这是一个[listQuery]示例应用');
    }

    // 下拉列表(下拉框)
    public static function selector()
    {
        return app('json')->success('您好！这是一个[selector]示例应用');
    }

    // 错误返回
    public static function fail()
    {
        return app('json')->fail('您好！这是一个[fail]示例应用');
    }
}