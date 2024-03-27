<?php
/**
 * Desc: 公共逻辑方法（基类）
 * User: lee
 * Date-Time: 2024/3/7/16:35
 */

namespace basic;

class BaseLogic
{
    // 预设常量
    public const CENTER_AUTO_LISTS_ROWS = 1000; // 定时任务 - 每次查询固定数量
    public const CENTER_AUTO_LIMIT = 100; // 定时任务 - 批量每次插入数据库数量
    public const BATCH_INSERT_LIMIT = 100; // 批量新增每批数据量

    /**
     * 金额计算
     * @param $n1:金额1
     * @param $symbol:加减乘除
     * @param $n2:金额2
     * @param string $scale 保留小数
     * @return string|null
     * User lee
     */
    public static function pricecalc($n1, $symbol, $n2, $scale = '2')
    {
        $res = "";
        switch ($symbol) {
            case "+"://加法
                $res = bcadd($n1, $n2, $scale);
                break;
            case "-"://减法
                $res = bcsub($n1, $n2, $scale);
                break;
            case "*"://乘法
                $res = bcmul($n1, $n2, $scale);
                break;
            case "/"://除法
                $res = bcdiv($n1, $n2, $scale);
                break;
            case "%"://求余、取模
                $res = bcmod($n1, $n2, $scale);
                break;
            default:
                $res = "";
                break;
        }
        return $res;
    }

    /**
     * 价格由元转分(用于微信支付单位转换)
     * @param $price:金额
     * @return int
     * User lee
     */
    public static function priceyuantofen($price)
    {
        return intval(self::pricecalc(100, "*",$price));
    }

    /**
     * 价格由分转元
     * @param $price:金额
     * @return string|null
     * User lee
     */
    public static function pricefentoyuan($price)
    {
        return self::pricecalc(self::priceformat($price),"/",100);
    }

    /**
     * 价格格式化
     * @param $price:金额
     * @return string
     * User lee
     */
    public static function priceformat($price, $decimals = 2)
    {
        return number_format($price, $decimals, '.', '');
    }

    /**
     * 获取性别
     * @param int $key 状态 key
     * @param int $isReverse 是否翻转状态 1 通过val值获取key  0 通过key获取val值
     * @return string|string[]
     * author lee
     */
    public static function getGenderArr($key = null, $isReverse = 0)
    {
        $selectArr = [0 => '未知', 1 => '男', 2 => '女'];

        if ($isReverse) $selectArr = array_flip($selectArr);

        if ($key || $key === 0) return $selectArr[$key];
    }

    /***
     * 无限极递归分类处理
     * @param $items
     * @return array
     * @author lee
     */
    public static function generateTree($items)
    {
        $index_items = [];
        foreach ($items as $item) {
            $index_items[$item['id']] = $item;
        }
        $tree = array();
        foreach ($index_items as $item) {

            if (isset($index_items[$item['pid']])) {
                $index_items[$item['pid']]['child'][] = &$index_items[$item['id']];
            } else {
                $tree[] = &$index_items[$item['id']];
            }
        }
        return $tree;
    }

    /**
     * 生成随机数
     * @param $len
     * @return array|int|string|string[]
     * @author lee
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

    /**
     * 判断是否多维数组
     * @param array $array
     * @return bool
     * @author lee
     * @date 2023/7/27 16:34
     */
    static function is_multi_array(array $array = [])
    {
        if (is_array($array)) {
            $count = count($array, COUNT_RECURSIVE);
            return ($count > count($array)) ? true : false;
        }
        return false;
    }

    /**
     * Desc: 多维数据去重
     * @param $array
     * @return array
     * @exception Exception
     * @author lee
     * @date 2023/10/31/16:12
     */
    public static function array_multi_unique($array,$fields):array
    {
        $result = [];
        $serialized = [];

        foreach ($array as $item) {
            $key = '';

            foreach ($fields as $field) {
                $key .= $item[$field];
            }

            if (!isset($result[$key])) {
                $result[$key] = $item;
            }
        }

        return array_values($result);
    }
}