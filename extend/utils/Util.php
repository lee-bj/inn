<?php
/**
 * Desc: 接参处理
 * User: 黎笔家
 * Date-Time: 2024/3/11/15:35
 */

namespace utils;

class Util
{
    /**
     * Desc: 获取POST请求的数据
     * @param $params:参数名
     * @param $request:请求体
     * @param $suffix:是否以数组接收
     * @return array
     * @exception Exception
     * @author 黎笔家
     * @date 2024/3/11/15:35
     */
    public static function postMore($params, $request = null, $suffix = false)
    {
        if ($request === null) $request = app('request');
        $p = [];
        $i = 0;
        foreach ($params as $param) {
            if (!is_array($param)) {
                $p[$suffix == true ? $i++ : $param] = $request->param($param);
            } else {
                if (!isset($param[1])) $param[1] = null;
                if (!isset($param[2])) $param[2] = '';
                if (is_array($param[0])) {
                    $name = is_array($param[1]) ? $param[0][0] . '/a' : $param[0][0] . '/' . $param[0][1];
                    $keyName = $param[0][0];
                } else {
                    $name = is_array($param[1]) ? $param[0] . '/a' : $param[0];
                    $keyName = $param[0];
                }
                $p[$suffix == true ? $i++ : (isset($param[3]) ? $param[3] : $keyName)] = $request->param($name, $param[1], $param[2]);
            }
        }
        return $p;
    }

    /**
     * Desc: 获取请求的数据
     * @param $params:参数名
     * @param $request:请求体
     * @param $suffix:是否以数组接收
     * @return array
     * @exception Exception
     * @author 黎笔家
     * @date 2024/3/11/15:36
     */
    public static function getMore($params, $request = null, $suffix = false)
    {
        if ($request === null) $request = app('request');
        $p = [];
        $i = 0;
        foreach ($params as $param) {
            if (!is_array($param)) {
                $p[$suffix == true ? $i++ : $param] = $request->param($param);
            } else {
                if (!isset($param[1])) $param[1] = null;
                if (!isset($param[2])) $param[2] = '';
                if (is_array($param[0])) {
                    $name = is_array($param[1]) ? $param[0][0] . '/a' : $param[0][0] . '/' . $param[0][1];
                    $keyName = $param[0][0];
                } else {
                    $name = is_array($param[1]) ? $param[0] . '/a' : $param[0];
                    $keyName = $param[0];
                }
                $p[$suffix == true ? $i++ : (isset($param[3]) ? $param[3] : $keyName)] = $request->param($name, $param[1], $param[2]);
            }
        }
        return $p;
    }
}