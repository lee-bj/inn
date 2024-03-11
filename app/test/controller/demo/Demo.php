<?php
declare (strict_types = 1);

namespace app\test\controller\demo;

use app\test\logic\demo\DemoLogic;

class Demo
{
    // 新增
    public function create()
    {
        return DemoLogic::create();
    }

    // 删除
    public function delete()
    {
        return DemoLogic::update();
    }

    // 修改（全）
    public function update()
    {
        return DemoLogic::update();
    }

    // 修改（局）
    public function edit($id)
    {
        return DemoLogic::edit();
    }

    // 详情
    public function detail()
    {
        return DemoLogic::update();
    }

    // 分页查询
    public function pageQuery()
    {
        return DemoLogic::pageQuery();
    }

    // 列表(无分页)
    public function listQuery()
    {
        return DemoLogic::listQuery();
    }

    // 下拉列表(下拉框)
    public function selector()
    {
        return DemoLogic::selector();
    }

    // 错误返回
    public function fail()
    {
        return DemoLogic::fail();
    }
}
