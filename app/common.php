<?php
// 应用公共文件
if (!function_exists('md5Salt')) {
    /**
     * Desc: md5加盐
     * @param string $str 加密字符串
     * @param string $salt 需要加的盐
     * @return string
     * @exception Exception
     * @author 黎笔家
     * @date 2024/1/9/10:06
     */
    function md5Salt(string $str = '', string $salt = 'sjzy')
    {
        if (!$str) throw new exception\ThrottleException(lang('encryptedCharacter')); // 请输入需要加密字符

        return md5(md5($str . '@#_' . $salt));
    }
}

if (!function_exists('validateDate')) {
    /**
     * Desc: 验证时间格式
     * @param $date
     * @param $format
     * @return bool
     * @exception Exception
     * @author 黎笔家
     * @date 2024/1/9/10:06
     */
    function validateDate($date = '', $format = 'Y-m-d H:i:s')
    {
        $time = DateTime::createFromFormat($format, $date);

        return $time && $time->format($format) === $date;
    }
}


if (!function_exists('get_last_day')) {
    /**
     * 获取月末最后一天
     * @param int $month 月份
     * @param string $time 指定时间戳，默认当前时间戳
     * @return false|string
     * User 黎笔家
     * Date 2024/1/9/10:06
     */
    function get_last_day(int $month = 01, string $time = '')
    {
        if (!$time) $time = time();

        $first_day = date('Y-' . $month . '-01', $time);

        return date('Y-m-d 23:59:59', strtotime("$first_day +1 month -1 day"));
    }
}

if (!function_exists('setLog')) {
    /**
     * 写入日志
     * @param $str :写入的内容
     * @param string $fileName :写入的文件，默认日期
     * @param string $pathName :写入的目录，默认/runtime/tryErrorLog/log
     * User 黎笔家
     *  Date 2024/1/9
     */
    function setLog($str, $fileName = 'log', $pathName = 'log')
    {
        $path = app()->getRootPath() . '/runtime/tryErrorLog/' . $pathName . '/' . date('Ymd');
        if (!is_dir($path)) mkdir($path, 0775, true);
        $file = $path . '/' . $fileName . '.txt';
        $str = "\r\n" . "\r\n" . date('Y-m-d H:i:s') . "\r\n------------------------\r\n" . $str;
        @file_put_contents($file, $str, FILE_APPEND);
    }
}

if (!function_exists('tryErrorLog')) {
    /**
     * 记录错误日志
     * @param $e
     * @param string $operation
     * @param string $remark
     * @param string $pathName
     * User 黎笔家
     * Date 2024/1/9
     */
    function tryErrorLog($e, $operation = 'select', string $remark = '', $pathName = 'log')
    {
        // 不记录400的错误
        if ($e->getCode() === 400) {
            return;
        }
        $request = request();
        $header = print_r($request->header(), true);
        $param = print_r($request->param(), true);
        $tmp = <<<EOF
-----------------------------------------------------------------------------------------------
报错文件：{$e->getFile()}                                                       
报错行号：{$e->getLine()}                                                                                       
报错内容：{$e->getMessage()} 
错误备注：{$remark}
IP地址：  {$request->ip()}   
访问url： {$request->url()}
请求方式：{$request->method()}
--------------------------------------------------------------------------------------------------------------------
请求参数：{$param}
--------------------------------------------------------------------------------------------------------------------
请求头部：{$header}                                           
-----------------------------------------------------------------------------------------------------------------------
EOF;
        $text = '';
        $num = count(preg_split("/((\r?\n)|(\r\n?))/", $tmp));
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $tmp) as $key => $line) {
            if ($key === 0 || $key === $num - 1) {
                $text .= $line . PHP_EOL;
            } else {
                $text .= '| ' . $line . PHP_EOL;
            }
        }
        $fileName = $operation . '_' . $request->action() . '_' . $request->controller() . '_err';
        $pathName = $pathName . '/' . app('http')->getName();
        setLog($text, $fileName, $pathName);
    }
}

if (!function_exists('unsetEmptyArray')) {
    /**
     * 删除空数组
     * @param array $array
     * @return array
     * User 黎笔家
     * Date 2024/1/9
     */
    function unsetEmptyArray(array $array = [])
    {
        $data = [];

        foreach ($array as $k => $v) {
            if (!empty($v)) $data[$k] = $v;
        }

        return $data;
    }
}

if (!function_exists('getArrayColumn')) {
    /**
     * 获取数组中的某一列，如果确定是纯数组（不包含数据集、模型对象），请使用array_column(数组, 键名)
     * @param mixed $sourceArr 数组、数据集
     * @param string $keyName 列名
     * @return array 返回指定列的数组
     * User 黎笔家
     * Date 2024/1/9
     */
    function getArrayColumn($sourceArr, string $keyName): array
    {
        $return = [];
        $sourceArr = array_values(array_filter($sourceArr));
        if (!$sourceArr) {
            return $return;
        }

        foreach ($sourceArr as $val) {
            isset($val[$keyName]) && $return[] = $val[$keyName];
        }

        return $return;
    }
}

if (!function_exists('convertArrKey')) {
    /**
     * 将数据库中查出的列表以指定的id作为数组的键名，如果确定是纯数组（不包含数据集、模型对象），请使用array_column(数组, null, 键名)
     * @param mixed $arr 数组、数据集等
     * @param string $keyName 列名、键名
     * @return array
     * User 黎笔家
     * Date 2024/1/9
     */
    function convertArrKey($arr, string $keyName): array
    {
        $arr2 = [];
        foreach ($arr as $val) {
            $arr2[$val[$keyName]] = $val;
        }

        return $arr2;
    }
}

if (!function_exists('calculateVolume')) {
    /**
     * 计算体积（不含单位换算，如：立方厘米换算立方米等）
     * @param mixed $length 长
     * @param mixed $width 宽
     * @param mixed $height 高
     * @param mixed $count 数量
     * @return string
     * User 黎笔家
     * Date 2024/1/9
     */
    function calculateVolume($length, $width, $height, $count = '1'): string
    {
        if (!is_string($length)) {
            $length = (string)$length;
        }

        if (!is_string($width)) {
            $width = (string)$width;
        }

        if (!is_string($height)) {
            $height = (string)$height;
        }

        if (!is_string($count)) {
            $count = (string)$count;
        }

        $squareMeasure = bcmul($length, $width, 20);
        $volume = bcmul($squareMeasure, $height, 20);

        return bcmul($volume, $count, 20);
    }
}

if (!function_exists('getKey')) {
    /**
     * Desc: 生成随机字符串
     * @param $type 1-全数字，2-全英文，3-英文数字，4-全小写英文，5-全大写英文，6-包含特殊符号
     * @param $qty 长度
     * @return string
     * User 黎笔家
     * Date 2024/1/9
     */
    function getKey($type, int $qty): string
    {
        $str = '';
        $chars = [
            "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
            "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
            "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
            "u", "v", "w", "x", "y", "z", "A", "B", "C", "D",
            "E", "F", "G", "H", "I", "J", "K", "L", "M", "N",
            "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X",
            "Y", "Z", ".", ",", "+", "-", "!", "#", '$', '%',
            '^', '[', '*', '(', ')', '_', '=', '~', '`', '?'
        ];
        switch ($type) {
            case 1:
                for ($i = 0; $i < $qty; $i++) {
                    $num = rand(0, 9);
                    $str .= $chars[$num];
                }
                break;
            case 2:
                for ($i = 0; $i < $qty; $i++) {
                    $num = rand(10, 56);
                    $str .= $chars[$num];
                }
                break;
            case 3:
                for ($i = 0; $i < $qty; $i++) {
                    $num = rand(0, 56);
                    $str .= $chars[$num];
                }
                break;
            case 4:
                for ($i = 0; $i < $qty; $i++) {
                    $num = rand(10, 35);
                    $str .= $chars[$num];
                }
                break;
            case 5:
                for ($i = 0; $i < $qty; $i++) {
                    $num = rand(36, 56);
                    $str .= $chars[$num];
                }
                break;
            case 6:
                for ($i = 0; $i < $qty; $i++) {
                    $num = rand(0, 71);
                    $str .= $chars[$num];
                }
                break;
            default:
                break;
        }
        return $str;
    }
}

if (!function_exists('getUniqueIdentity')) {
    /**
     * Desc:生成唯一标识
     * @param $type 0订单，1服务单
     * @return string
     * User 黎笔家
     * Date 2024/1/9
     */
    function getUniqueIdentity($type = 0): string
    {
        switch ($type) {
            case 1:
                $str = "FWO";
                break;
            default:
                $str = "O";
        }

        return $str . date('ymd') . '-' . getKey(5, 4) . '-' . getKey(5, 4) . '-' . getKey(5, 4);
    }
}