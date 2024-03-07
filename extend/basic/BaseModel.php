<?php
/**
 * Desc: 公共model方法（基类）
 * User: lee
 * Date-Time: 2024/3/7/16:36
 */

namespace basic;

use think\facade\Db;
use think\Model;

class BaseModel  extends Model
{
    public const DEFAULT_ORDER = 'id DESC'; // 默认排序
    public const DEFAULT_PAGE = ['page' => 1, 'list_rows' => 10]; // 默认页码，显示数量

    // status - 状态
    public const ALL = '';//全部
    public const DEL_CODE = 0; // 删除
    public const NORMAL = 1; // 正常
    public const DISABLE = 2; // 禁用
    public const IN_STATUS = [self::NORMAL, self::DISABLE]; // 正常与禁用

    private static $errorMsg;

    // 正常与禁用
    public const IN_STATUS_TEXT = [
        self::NORMAL => '正常',
        self::DISABLE => '禁用'
    ];

    // 登录设备 1 windows，2 android，3 ios，4 mac，5 imac，6 pda，7 agv，8 未知
    const DEVICE_1 = 1; // windows
    const DEVICE_2 = 2; // android
    const DEVICE_3 = 3; // ios
    const DEVICE_4 = 4; // mac
    const DEVICE_5 = 5; // imac
    const DEVICE_6 = 6; // 未知
    const DEVICE = [
        self::DEVICE_1 => 'windows',
        self::DEVICE_2 => 'android',
        self::DEVICE_3 => 'ios',
        self::DEVICE_4 => 'mac',
        self::DEVICE_5 => 'imac',
        self::DEVICE_6 => '未知'
    ];

    const DEFAULT_ERROR_MSG = '操作失败,请稍候再试!';

    /**
     * Desc: 获取 正常与禁用 状态
     * @param array|int[] $status
     * @return BaseModel
     * @exception Exception
     * @author lee
     * @date 2024/1/9/9:53
     */
    public static function getStatus(array $status = self::IN_STATUS)
    {
        return self::whereIn('status', $status);
    }

    /**
     * Desc: 设置错误信息
     * @param $errorMsg
     * @param $rollback
     * @return false
     * @exception Exception
     * @author lee
     * @date 2024/1/9/9:55
     */
    protected static function setErrorInfo($errorMsg = self::DEFAULT_ERROR_MSG, $rollback = false)
    {
        if ($rollback) self::rollbackTrans();
        self::$errorMsg = $errorMsg;
        return false;
    }

    /**
     * Desc: 获取错误信息
     * @param $defaultMsg
     * @return mixed
     * @exception Exception
     * @author lee
     * @date 2024/1/9/9:55
     */
    public static function getErrorInfo($defaultMsg = self::DEFAULT_ERROR_MSG)
    {
        return !empty(self::$errorMsg) ? self::$errorMsg : $defaultMsg;
    }

    /**
     * Desc: 开启事务
     * @exception Exception
     * @author lee
     * @date 2024/1/9/9:55
     */
    public static function beginTrans()
    {
        Db::startTrans();
    }

    /**
     * Desc: 提交事务
     * @exception Exception
     * @author lee
     * @date 2024/1/9/9:55
     */
    public static function commitTrans()
    {
        Db::commit();
    }

    /**
     * Desc: 关闭事务
     * @exception Exception
     * @author lee
     * @date 2024/1/9/9:56
     */
    public static function rollbackTrans()
    {
        Db::rollback();
    }

    /**
     * Desc: 根据结果提交滚回事务
     * @param $res
     * @exception Exception
     * @author lee
     * @date 2024/1/9/9:56
     */
    public static function checkTrans($res)
    {
        if ($res) {
            self::commitTrans();
        } else {
            self::rollbackTrans();
        }
    }
}