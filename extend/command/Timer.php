<?php
/**
 * Desc: 定时任务 基础配置处理
 * User: 黎笔家
 * Date-Time: 2024/1/9/10:45
 */

namespace command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use Workerman\Worker;

class Timer extends Command
{

    protected $timer;
    protected $interval = 2;

    protected function configure()
    {
        // 指令配置
        $this->setName('timer')
            ->addArgument('status', Argument::REQUIRED, 'start/stop/reload/status/connections')
            ->addOption('d', null, Option::VALUE_NONE, 'daemon（守护进程）方式启动')
            ->addOption('i', null, Option::VALUE_OPTIONAL, '多长时间执行一次,可以精确到0.001')
            ->setDescription('start/stop/restart 定时任务, 启动是加上 --d 为守护进程模式');
    }

    protected function init(Input $input, Output $output)
    {
        global $argv;

        if ($input->hasOption('i'))
            $this->interval = floatval($input->getOption('i'));

        $argv[1] = $input->getArgument('status') ?: 'start';
        if ($input->hasOption('d')) {
            $argv[2] = '-d';
        } else {
            unset($argv[2]);
        }
    }

    protected function execute(Input $input, Output $output)
    {
        $this->init($input, $output);
        Worker::$pidFile = app()->getRootPath().'timer.pid';
        $task = new Worker();
        $task->count = 1;
        event('Task_10');
        $task->onWorkerStart = [$this, 'start'];
        $task->runAll();
    }

    public function stop()
    {
        \Workerman\Lib\Timer::del($this->timer);
    }

    public function start()
    {
        $last = time();

        // 执行时间，需要与 subscribes\TaskSubscribe.php 匹配
        $task = [
            2 => $last,
            10 => $last,
            30 => $last,
            60 => $last,
            180 => $last,
            300 => $last,
            600 => $last,
            3600 => $last,
            7200 => $last
        ];

        $this->timer = \Workerman\Lib\Timer::add($this->interval, function () use (&$task) {
            try {
                $now = time();
                event('Task_2');
                foreach ($task as $sec => $time) {
                    if ($now - $time >= $sec) {
                        event('Task_' . $sec);
                        $task[$sec] = $now;
                    }
                }
            } catch (\Throwable $e) {
                throw $e;
            }
        });
    }
}