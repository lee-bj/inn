<?php
declare (strict_types=1);

/**
 * Desc: 创建模型
 * User: 黎笔家
 * Date-Time: 2024/1/9/9:40
 */

namespace command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class Table extends Command
{

    protected function configure()
    {
        // 指令配置
        $this->setName('table')
            ->addArgument('name')
            ->addArgument('model')
            ->addArgument('space')
            ->setDescription('Build Model 模块，php think table 表名 模型名 生成的目录(app\test\model\system)');

    }
    //  php think table sys_notice_message SysMenuModel app\admin\system
    //                  表名                模型名        生成的目录
    protected function execute(Input $input, Output $output)
    {
        $tableName = $input->getArgument('name');
        $modelName = $input->getArgument('model');
        $nameSpace = $input->getArgument('space');

        if (!$tableName || !$modelName || !$nameSpace) {
            echo '参数依次为 [表全名] [模型名称] [命名空间]';
            exit();
        }
        $baseModelPath = $nameSpace;

        // 判断继承模型基类
        $tpl['inheritModel'] = 'BaseModel';
        if (isset(explode('\wms\\', $baseModelPath)[1])) $tpl['inheritModel'] = 'BaseWmsModel';

        if (!is_dir($baseModelPath)) {
            mkdir($baseModelPath, 0777, true);
        }
        $fullModelFile = $baseModelPath . '/' . $modelName . '.php';
        if (file_exists($fullModelFile)) {
            echo $fullModelFile . '已经存在';
            exit();
        }

        $columns = Db::query("SHOW FULL COLUMNS FROM  $tableName");

        $tpl['modelName'] = $modelName;
        $tpl['tableName'] = $tableName;
        $tpl['nameSpace'] = str_replace('/', '\\', $baseModelPath);
        $tpl['columns'] = $columns;
        $tplpath = app()->getRootPath() . 'extend' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'TableTpl.php';

        ob_start();
        require $tplpath;
        $out = ob_get_clean();
        file_put_contents($fullModelFile, $out);

        $output->writeln("<info>创建成功</info>");
    }
}