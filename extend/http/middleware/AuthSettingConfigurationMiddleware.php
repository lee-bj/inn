<?php
/**
 * Desc: 自动设置配置
 * User: lee
 * Date-Time: 2024/3/7/15:30
 */

namespace http\middleware;

use http\interfaces\MiddlewareInterface;
use app\Request;
use basic\BaseModel;
use exception\ThrottleException;
use think\facade\Env;
class AuthSettingConfigurationMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, \Closure $next, bool $force = true)
    {
        self::getDeviceId($request); // 获取当前使用设备

        return $next($request);
    }

    /**
     * Desc: 获取当前使用设备
     * @param $request
     * @return mixed
     * @exception Exception
     * @author lee
     * @date 2024/3/7/15:31
     */
    private static function getDeviceId($request)
    {
        $server = $request->server();
        $device = '';
        $device_id = BaseModel::DEVICE_6; // 默认为未知设备

        // 获取设备型号
        if (isset($server['OS'])) { // window 端获取
            $device = strtoupper($server['OS']);
        } elseif (isset($server['HTTP_USER_AGENT'])) { // 其他端 获取
            $device = strtoupper($server['HTTP_USER_AGENT']);
        }

        // 登录设备 1 window，2 android，3 ios，4 mac，5 imac，6 未知
        if (strstr($device, 'WINDOW')) $device_id = BaseModel::DEVICE_1;
        if (strstr($device, 'ANDROID')) $device_id = BaseModel::DEVICE_2;
        if (strstr($device, 'IOS')) $device_id = BaseModel::DEVICE_3;
        if (strstr($device, 'IMAC')) $device_id = BaseModel::DEVICE_5;
        if (strstr($device, 'MAC')) $device_id = BaseModel::DEVICE_4;

        // 设备id
        Request::macro('device_id', function () use ($device_id) { return $device_id; });

        return $device_id;
    }
}