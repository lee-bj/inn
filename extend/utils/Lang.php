<?php
/**
 * Desc 语言包配置
 * User 黎笔家
 * Date 2023/6/28
 */

namespace utils;

use exception\ThrottleException;

class Lang
{
    /**
     * 当header头语言包不合法，设置默认语言为中文
     * @return mixed|string
     * @throws ThrottleException
     * User 黎笔家
     * Date 2023/12/8
     */
    public static function getDefaultLang()
    {
        if (in_array(app('request')->header('Accept-Language'), self::getName())) return app('request')->header('Accept-Language');

        return env('lang.default_lang', 'cn');
    }

    /**
     * 获取语言包文件名
     * @param string $suffix 文件后缀
     * @return array|mixed
     * @throws ThrottleException
     * User 黎笔家
     * Date 2023/6/28
     */
    public static function getName($suffix = '.json')
    {
        if (!$suffix) throw new ThrottleException('缺少语言包后缀信息');

        return self::getLangFileConfig($suffix)['name'];
    }

    /**
     * 获取语言包文件路径
     * @param string $suffix 文件后缀
     * @return array
     * @throws ThrottleException
     * User 黎笔家
     * Date 2023/6/28
     */
    public static function getFileDir($suffix = '.json')
    {
        if (!$suffix) throw new ThrottleException('缺少语言包后缀信息');

        return self::getLangFileConfig($suffix)['file_dir'];
    }

    /**
     * 定义语言包路径
     * @return string
     * User 黎笔家
     * Date 2023/6/28
     */
    protected static function getDir()
    {
        return app()->getRootPath().'extend/lang';
    }

    /**
     * 获取语言包信息
     * @param string $suffix
     * User 黎笔家
     * Date 2023/6/28
     */
    protected static function getLangFileConfig(string $suffix)
    {
        $files = scandir(self::getDir());
        $dir_array = ['name' => [], 'file_dir' => []];

        if (is_array($files)) {
            $dir_name = [];
            $dir_file_dir = [];

            foreach ($files as $val) {
                // 跳过. 和 .. 和 目录
                if ($val == '.' || $val == '..' || is_dir(self::getDir() . '/' . $val))
                    continue;

                // 跳过 不支持的文件后缀
                $name = explode($suffix, $val);
                if (!isset($name[1]))
                    continue;

                // 将当前文件添加进数组
                $dir_name[] = $name[0];
                $dir_file_dir[$name[0]] = self::getDir() . '/' . $val;
            }

            $dir_array = ['name' => $dir_name, 'file_dir' => $dir_file_dir];

        }

        return $dir_array;
    }
}