<?php
/**
 * Desc: 创建模块
 * User: 黎笔家
 * Date-Time: 2024/1/9/9:37
 */

namespace command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class Module extends Command
{
    /**
     * 应用基础目录
     * @var string
     */
    protected $basePath;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('build')
            ->addArgument('app', Argument::OPTIONAL, 'app name .')
            ->setDescription('生成 App 模块层，php think module admin\admin(应用名\模块名)');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->basePath = $this->app->getBasePath();
        $app            = $input->getArgument('app') ?: '';

        if (is_file($this->basePath . 'build.php')) {
            $list = include $this->basePath . 'build.php';
        } else {
            $list = ['controller', 'logic', 'model', 'validate'];
        }

        $this->buildApp($app, $list);

        $output->writeln("<info>创建成功</info>");
    }

    /**
     * 创建应用
     * @access protected
     * @param  string $app  应用名
     * @param  array  $list 目录结构
     * @return void
     */
    protected function buildApp(string $app, array $list = []): void
    {
        $appUrl = explode('\\', $app);
        if (empty($appUrl[0]) || empty($appUrl[1])) dd('路径格式有误，请以此格式使用：admin\admin (应用名\模块名)');

        if (!is_dir($this->basePath . $appUrl[0])) dd('路径格式有误，请先生成应用层');

        $appPath   = $this->basePath . ($appUrl[0] ? $appUrl[0] . DIRECTORY_SEPARATOR : '');
        $namespace = 'app' . ($appUrl[0] ? '\\' . $appUrl[0] : '');
        $content = '';
        $text = ucfirst($appUrl[1]);
        // 如果是wms的内容，修改基础model，继承统一表后缀
        $inheritModel = $appUrl[0] == 'wms' ? 'BaseWmsModel' : 'BaseModel';

        foreach ($list as $dir) {
            $nameUrl = '';
            switch ($dir) {
                case 'controller': // 控制器
                    $nameUrl = $namespace . '\\' . $dir . DIRECTORY_SEPARATOR . $appUrl[1];

                    $filename = $appPath . DIRECTORY_SEPARATOR . $dir. DIRECTORY_SEPARATOR . $appUrl[1] . DIRECTORY_SEPARATOR . $text .'.php';
                    $this->checkDirBuild(dirname($filename));

                    $content = file_get_contents(app()->getRootPath() . 'extend' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'TestControllerTpl.stub');
                    $content = str_replace(['{%app%}', '{%dir%}', '{%date%}', '{%layer%}', '{%text%}'], [$nameUrl,  $appUrl[0], date('Y/m/d'), $appUrl[1], $text], $content);
                    break;
                case 'model': // 模型
                    $nameUrl = $namespace . '\\' . $dir . DIRECTORY_SEPARATOR . $appUrl[1];

                    $filename = $appPath . DIRECTORY_SEPARATOR . $dir. DIRECTORY_SEPARATOR . $appUrl[1] . DIRECTORY_SEPARATOR . $text . 'Model.php';
                    $this->checkDirBuild(dirname($filename));

                    $content = file_get_contents(app()->getRootPath() . 'extend' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'TestModelTpl.stub');
                    $content = str_replace(['{%app%}', '{%date%}', '{%text%}', '{%inheritModel%}'], [$nameUrl, date('Y/m/d'), $text, $inheritModel], $content);
                    break;
                case 'logic': // 逻辑
                    $nameUrl = $namespace . '\\' . $dir . DIRECTORY_SEPARATOR . $appUrl[1];

                    $filename = $appPath . DIRECTORY_SEPARATOR . $dir. DIRECTORY_SEPARATOR . $appUrl[1] . DIRECTORY_SEPARATOR . $text . 'Logic.php';
                    $this->checkDirBuild(dirname($filename));

                    $content = file_get_contents(app()->getRootPath() . 'extend' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'TestLogicTpl.stub');
                    $content = str_replace(['{%app%}', '{%date%}', '{%text%}'], [$nameUrl, date('Y/m/d'), $text], $content);
                    break;
                case 'validate': // 验证器
                    $nameUrl = $namespace . '\\' . $dir . DIRECTORY_SEPARATOR . $appUrl[1];

                    $filename = $appPath . DIRECTORY_SEPARATOR . $dir. DIRECTORY_SEPARATOR . $appUrl[1] . DIRECTORY_SEPARATOR . $text .'Validate.php';
                    $this->checkDirBuild(dirname($filename));

                    $content = file_get_contents(app()->getRootPath() . 'extend' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'TestValidateTpl.stub');
                    $content = str_replace(['{%app%}', '{%date%}', '{%text%}'], [$nameUrl, date('Y/m/d'), $text], $content);
                    break;
                default:
                    // 其他文件
                    $content = '';
            }

            if (!is_file($filename)) {
                file_put_contents($filename, $content);
            }
        }
    }

    /**
     * 创建目录
     * @access protected
     * @param  string $dirname 目录名称
     * @return void
     */
    protected function checkDirBuild(string $dirname): void
    {
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
    }
}