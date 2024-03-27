<?php
/**
 * Desc Redis拓展
 * User 黎笔家
 * Date 2023/1/31
 */


namespace utils;


use think\facade\Config;

class Redis
{
    protected $config;
    public $redis;

    public function __construct()
    {
        $this->config=Config::get('redis');
        $this->redis=new \Redis();
        $this->redis->connect($this->config['host'],intval($this->config['port']));
        if($this->config['password']) $this->redis->auth($this->config['password']);
    }

    public function getRedis()
    {
        $this->redis->select(env('redis.select', 0));
        return $this->redis;
    }
}