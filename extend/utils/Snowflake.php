<?php
/**
 * Desc 生成唯一编码
 * User 黎笔家
 * Date 2023/7/4
 */

namespace utils;
use exception\ThrottleException;

class Snowflake
{
    const EPOCH = 1654495724726;    //开始时间,固定一个小于当前时间的毫秒数
    const max12bit = 4095;
    const max41bit = 1099511627775;

    /**
     * 生成唯一数字串
     * @param int|null $machineId
     * @return float|int
     * User 黎笔家
     * Date 2023/7/4
     */
    private static function createOnlyId(int $machineId = null)
    {
        // 时间戳 42字节
        $time = floor(microtime(true) * 1000);
        // 当前时间 与 开始时间 差值
        $time -= self::EPOCH;
        // 二进制的 毫秒级时间戳
        $base = decbin(self::max41bit + $time);
        // 机器id 10 字节
        if ($machineId) $machineId = str_pad(decbin($machineId), 10, "0", STR_PAD_LEFT);
        // 序列数 12字节
        $random = str_pad(decbin(mt_rand(0, self::max12bit)), 12, "0", STR_PAD_LEFT);
        // 拼接
        $base = $base . $machineId . $random;
        // 转化为 十进制 返回
        return bindec($base);
    }

    /**
     * 生成唯一ID
     * @param string $prefix 前缀码
     * @param int $machineId 机器id（一般不填）
     * @return string
     * User 黎笔家
     * Date 2023/7/4
     */
    public static function createId($prefix = '', int $machineId = 1)
    {
        return $prefix . self::createOnlyId($machineId) . mt_rand(10, 99) . mt_rand(10, 99);
    }

    /**
     * 自动生成秘钥
     * @param string $prefix 前缀
     * @param int $length 自动补充字符长度
     * @param int $second 过期时间，默认到0点更新 date('Y-m-d H:i:s', strtotime('tomorrow') . ' // 明天0点<br/>'
     * @return string
     * @throws ThrottleException
     * User 黎笔家
     * Date 2023/7/14
     */
    public static function creatrRedisCodeKey($prefix = '', $length = 4, $second = 0)
    {
        if (!$prefix) throw new ThrottleException('缺少生成秘钥前缀', 422);

        $key = $prefix . date('Ymd');

        $redis = (new Redis())->getRedis();
        if (!$redis->exists($key)) {
            $expire = true;//第一次设置过期时间
        }
        $redis->incr($key);

        if (!$second) $second = strtotime('tomorrow') - time();
        isset($expire) && $redis->expire($key, $second);

        return $key . str_pad($redis->get($key), $length, '0', STR_PAD_LEFT);
    }

    /**
     * 生成随机数
     * @param $len
     * @return array|int|string|string[]
     * @author 黎笔家
     * @date 2023/7/14 17:23
     */
    public static function randStr( $len = 6 )
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';

        $string = time();

        for(;$len >= 1;$len--)
        {

            $position = rand()%strlen($chars);

            $position2 = rand()%strlen($string);

            $string = substr_replace($string,substr($chars,$position,1),$position2,0);

        }

        return $string;
    }
}