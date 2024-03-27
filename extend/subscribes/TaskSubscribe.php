<?php
/**
 * Desc 定时任务类
 * User 黎笔家
 * Date 2023/1/20
 */

namespace subscribes;

use basic\BaseLogic;

class TaskSubscribe
{
    const FILENAME_SUCCESS = 'timer_task_success';
    const FILENAME_FAIL = 'timer_task_fail';

    public function handle()
    {
    }

    public function __construct()
    {
        // 设置系统-执行身份
        if (app('request')->ip() != '0.0.0.0') return;

    }

    /**
     * 2秒钟执行的方法 setLog('2秒定时任务');
     * User 黎笔家
     * Date 2023/1/29
     */
    public function onTask_2()
    {
    }

    /**
     * 10秒钟执行的方法 setLog('10秒定时任务');
     * User 黎笔家
     * Date 2023/1/29
     */
    public function onTask_10()
    {

    }

    /**
     * 30秒钟执行的方法 setLog('30秒定时任务');
     * User 黎笔家
     * Date 2023/1/29
     */
    public function onTask_30()
    {
    }

    /**
     * 60秒钟执行的方法 setLog('60秒定时任务');
     * User 黎笔家
     * Date 2023/1/29
     */
    public function onTask_60()
    {

    }

    /**
     * 180秒钟执行的方法 setLog('180秒定时任务');
     * User 黎笔家
     * Date 2023/1/29
     */
    public function onTask_180()
    {
    }

    /**
     * 300秒钟执行的方法 setLog('300秒定时任务');
     * User 黎笔家
     * Date 2023/1/29
     */
    public function onTask_300()
    {
    }

    /**
     * 600秒钟执行的方法 setLog('600秒定时任务');
     * User 黎笔家
     * Date 2023/1/29
     */
    public function onTask_600()
    {
    }

    /**
     * 3600秒钟执行的方法 setLog('3600秒定时任务');
     * User 黎笔家
     * Date 2023/1/29
     */
    public function onTask_3600()
    {
    }

    /**
     * 7200秒钟执行的方法 setLog('7200秒定时任务');
     * User 黎笔家
     * Date 2023/1/29
     */
    public function onTask_7200()
    {
    }
}