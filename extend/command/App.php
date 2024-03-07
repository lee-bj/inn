<?php
/**
 * Desc: 创建应用
 * User: lee
 * Date-Time: 2024/3/7/9:48
 */

namespace command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class App extends Command
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
            ->setDescription('生成 App 应用层，php think app admin(应用名)');
    }

    protected function execute(Input $input, Output $output)
    {
        $this->basePath = $this->app->getBasePath();
        $app = $input->getArgument('app') ?: '';

        if (is_file($this->basePath . 'build.php')) {
            $list = include $this->basePath . 'build.php';
        } else {
            $list = [
                '__app__' => ['controller', 'logic', 'model', 'validate', 'route', 'config'],
            ];
        }

        $this->buildApp($app, $list);
        $output->writeln("<info>创建成功</info>");

    }

    /**
     * 创建应用
     * @access protected
     * @param string $app 应用名
     * @param array $list 目录结构
     * @return void
     */
    protected function buildApp(string $app, array $list = []): void
    {

        if (!is_dir($this->basePath . $app)) {
            // 创建应用目录
            mkdir($this->basePath . $app);
        }

        $appPath = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '');
        $namespace = 'app' . ($app ? '\\' . $app : '');
        $content = '';

        // 创建配置文件和公共文件
        $this->buildCommon($app);
        // 创建模块的默认页面
        $this->buildHello($app, $namespace);

        foreach ($list as $path => $file) {
            if ('__app__' == $path) {
                foreach ($file as $dir) {

                    switch ($dir) {
                        case 'controller': // 控制器
                            $filename = $appPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'index.php';
                            $this->checkDirBuild(dirname($filename));
                            break;
                        case 'model': // 模型
                            $filename = $appPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'index.php';
                            $this->checkDirBuild(dirname($filename));
                            $content = "<?php" . PHP_EOL . "// 这是系统自动生成的公共文件" . PHP_EOL;
                            break;
                        case 'view': // 视图
                            $filename = $appPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'index.html';
                            $this->checkDirBuild(dirname($filename));
                            $content = "<?php" . PHP_EOL . "// 这是系统自动生成的公共文件" . PHP_EOL;
                            break;
                        case 'logic': // 逻辑
                            $filename = $appPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'index.php';
                            $this->checkDirBuild(dirname($filename));
                            $content = "<?php" . PHP_EOL . "// 这是系统自动生成的公共文件" . PHP_EOL;
                            break;
                        case 'validate': // 验证器
                            $filename = $appPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'index.php';
                            $this->checkDirBuild(dirname($filename));
                            $content = "<?php" . PHP_EOL . "// 这是系统自动生成的公共文件" . PHP_EOL;
                            break;
                        case 'route': // 路由
                            $filename = $appPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'route.php';
                            $this->checkDirBuild(dirname($filename));
                            $content = file_get_contents(app()->getRootPath() . 'extend' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'RouteApiTpl.stub');
                            $content = str_replace(['{%app%}', '{%layer%}', '{%date%}'], [$namespace, 'controller', date('Y/m/d')], $content);
                            break;
                        case 'config': // 配置
                            $filename = $appPath . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . 'database.php';
                            $this->checkDirBuild(dirname($filename));
                            $content = file_get_contents(app()->getRootPath() . 'extend' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'DatabaseTpl.stub');
                            $content = str_replace(['{%app%}'], [$app], $content);
                            break;
                        default:
                            // 其他文件
                            $content = '';
                    }

                    if (!is_file($filename)) {
                        file_put_contents($filename, $content);
                    }
                }
            } elseif ('__file__' == $path) {
                // 生成（空白）文件
                foreach ($file as $name) {
                    if (!is_file($appPath . $name)) {
                        file_put_contents($appPath . $name, 'php' == pathinfo($name, PATHINFO_EXTENSION) ? '<?php' . PHP_EOL : '');
                    }
                }
            } else {
                // 生成相关MVC文件
                foreach ($file as $val) {
                    $val = trim($val);
                    $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . '.php';
                    $space = $namespace . '\\' . $path;
                    $class = $val;
                    switch ($path) {
                        case 'controller': // 控制器
                            if ($this->app->config->get('route.controller_suffix')) {
                                $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . 'Controller.php';
                                $class = $val . 'Controller';
                            }
                            $content = "<?php" . PHP_EOL . "namespace {$space};" . PHP_EOL . PHP_EOL . "class {$class}" . PHP_EOL . "{" . PHP_EOL . PHP_EOL . "}";
                            break;
                        case 'model': // 模型
                            $content = "<?php" . PHP_EOL . "namespace {$space};" . PHP_EOL . PHP_EOL . "use think\Model;" . PHP_EOL . PHP_EOL . "class {$class} extends Model" . PHP_EOL . "{" . PHP_EOL . PHP_EOL . "}";
                            break;
                        case 'view': // 视图
                            $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . '.html';
                            $this->checkDirBuild(dirname($filename));
                            $content = '';
                            break;
                        case 'logic': // 逻辑
                            $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . '.php';
                            $this->checkDirBuild(dirname($filename));
                            $content = '';
                            break;
                        case 'validate': // 验证器
                            $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . '.php';
                            $this->checkDirBuild(dirname($filename));
                            $content = '';
                            break;
                        case 'route': // 路由
                            $filename = $appPath . $path . DIRECTORY_SEPARATOR . $val . '.php';
                            $this->checkDirBuild(dirname($filename));
                            $content = '';
                            break;
                        default:
                            // 其他文件
                            $content = "<?php" . PHP_EOL . "namespace {$space};" . PHP_EOL . PHP_EOL . "class {$class}" . PHP_EOL . "{" . PHP_EOL . PHP_EOL . "}";
                    }

                    if (!is_file($filename)) {
                        file_put_contents($filename, $content);
                    }
                }
            }
        }
    }

    /**
     * 创建应用的欢迎页面
     * @access protected
     * @param string $app 目录
     * @param string $namespace 类库命名空间
     * @return void
     */
    protected function buildHello(string $app, string $namespace): void
    {
        $suffix = $this->app->config->get('route.controller_suffix') ? 'Controller' : '';
        $filename = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '') . 'controller' . DIRECTORY_SEPARATOR . 'Index' . $suffix . '.php';

        if (!is_file($filename)) {
            $content = file_get_contents(app()->getRootPath() . 'extend' . DIRECTORY_SEPARATOR . 'tpl' . DIRECTORY_SEPARATOR . 'AppTpl.stub');
            $content = str_replace(['{%name%}', '{%app%}', '{%layer%}', '{%suffix%}'], [$app, $namespace, 'controller', $suffix], $content);
            $this->checkDirBuild(dirname($filename));

            file_put_contents($filename, $content);
        }
    }

    /**
     * 创建应用的公共文件
     * @access protected
     * @param string $app 目录
     * @return void
     */
    protected function buildCommon(string $app): void
    {
        $appPath = $this->basePath . ($app ? $app . DIRECTORY_SEPARATOR : '');

        if (!is_file($appPath . 'common.php')) {
            file_put_contents($appPath . 'common.php', "<?php" . PHP_EOL . "// 这是系统自动生成的公共文件" . PHP_EOL);
        }

        foreach (['event', 'middleware', 'common'] as $name) {
            if (!is_file($appPath . $name . '.php')) {
                file_put_contents($appPath . $name . '.php',
                    $name != 'middleware'
                        ? "<?php" . PHP_EOL . "// 这是系统自动生成的{$name}定义文件" . PHP_EOL . "return [" . PHP_EOL . PHP_EOL . "];" . PHP_EOL
                        : "<?php" . PHP_EOL
                        . "// 这是系统自动生成的{$name}定义文件" . PHP_EOL
                        . "// 获取app 下的中间件配置" . PHP_EOL
                        . '$middleware = include app()->getRootPath().\'app/middleware.php\';' . PHP_EOL . PHP_EOL
                        . 'return array_merge($middleware, [' . PHP_EOL . PHP_EOL . ']);' . PHP_EOL
                );
            }
        }
    }

    /**
     * 创建目录
     * @access protected
     * @param string $dirname 目录名称
     * @return void
     */
    protected function checkDirBuild(string $dirname): void
    {
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
    }
}