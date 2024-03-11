<?php
/**
 * Desc: 公共验证方法（基类）
 * User: lee
 * Date-Time: 2024/3/7/16:38
 */

declare (strict_types=1);
namespace basic;

use think\exception\ValidateException;
use think\Validate;
use think\validate\ValidateRule;

class BaseValidate extends Validate
{
    /**
     * Desc: 自定义规则，排序 key值必须存在 field 和 type
     * @param $value
     * @param $rule
     * @param $param
     * @param $title_en
     * @param $title_cn
     * @return true
     * @exception Exception
     * @author lee
     * @date 2024/1/9/9:58
     */
    public function isArraySort($value, $rule, $param = [], $title_en = '', $title_cn = '')
    {
        $key = array_keys($value);

        // 排序 key值必须存在 field 和 type
        if (!in_array('field', $key) || !in_array('type', $key))
            throw new ValidateException(lang('validateSortKeyValueMustExistFieldAndType'));

        // 排序 type 传值仅支持 desc 或 asc
        if (!in_array($value['type'], ['asc', 'desc', 'ASC', 'DESC']))
            throw new ValidateException(lang('validateSortTypeValueIsOnlySupportedDescOrAsc'));

        return true;
    }

    /**
     * Desc: 自定义规则，数组数据不能为空
     * @param $value
     * @param $rule
     * @param $param
     * @param $title_en
     * @param $title_cn
     * @return true
     * @exception Exception
     * @author lee
     * @date 2024/1/9/9:59
     */
    public function isArray($value, $rule, $param = [], $title_en = '', $title_cn = '')
    {
        if (!is_array($value)) throw new ValidateException(lang('validateIdsRequire'));
        if (empty($value)) throw new ValidateException(lang('validateIdsRequire'));

        foreach ($value as $k => $v) {
            if (empty($v) && $v !== '0') {
                throw new ValidateException(lang('validateIdsRequire'));
            }
        }
        return true;
    }

    /**
     * Desc: 自定义规则，验证时间参数 字段名要求 start_time end_time
     * @param $value
     * @param $rule
     * @param $param
     * @param $title_en
     * @param $title_cn
     * @return true
     * @exception Exception
     * @author lee
     * @date 2024/1/9/10:00
     */
    public function isStartOrEndTime($value, $rule, $param = [], $title_en = '', $title_cn = '')
    {
        $start_title = explode('start_time', $title_en);
        $end_title = explode('end_time', $title_en);

        // 时间查询格式错误
        if (!validateDate($value))
            throw new ValidateException(lang('validateTimeQueryFormatError'));

        $head_title = '';
        $head_title = count($start_title) >= 2 ? $start_title[0] : $head_title;
        $head_title = count($end_title) >= 2 ? $end_title[0] : $head_title;

        if ($head_title && !empty($param[$head_title . 'start_time']) && !empty($param[$head_title . 'end_time'])) {
            // 开始时间 不能为空
            if (empty($param[$head_title . 'start_time']))
                throw new ValidateException(lang('validateStartTimeNotNull'));

            // 结束时间 不能为空
            if (empty($param[$head_title . 'end_time']))
                throw new ValidateException(lang('validateEndTimeNotNull'));

            // 时间查询格式错误
            if (!validateDate($param[$head_title . 'start_time']) || !validateDate($param[$head_title . 'end_time']))
                throw new ValidateException(lang('validateTimeQueryFormatError'));

            // 结束时间 不能小于等于 开始时间
            if (strtotime($param[$head_title . 'end_time']) <= strtotime($param[$head_title . 'start_time']))
                throw new ValidateException(lang('validateEndTimeLessThanOrEqualStartTime'));

            return true;
        }

        return true;
    }

    /**
     * Desc: 自定义规则，验证时间参数 字段名要求 字段[start_date, end_date]
     * @param $value
     * @param $rule
     * @param $param
     * @param $title_en
     * @param $title_cn
     * @return true
     * @exception Exception
     * @author lee
     * @date 2024/1/9/10:01
     */
    public function isStartOrEndDate($value, $rule, $param = [], $title_en = '', $title_cn = '')
    {
        // 开始时间 不能为空
        if (empty($value['start_date']))
            throw new ValidateException(lang('validateStartTimeNotNull'));

        // 结束时间 不能为空
        if (empty($value['end_date']))
            throw new ValidateException(lang('validateEndTimeNotNull'));

        // 时间查询格式错误
        if (!validateDate($value['start_date']) || !validateDate($value['end_date']))
            throw new ValidateException(lang('validateTimeQueryFormatError'));

        // 结束时间 不能小于等于 开始时间
        if (strtotime($value['end_date']) <= strtotime($value['start_date']))
            throw new ValidateException(lang('validateEndTimeLessThanOrEqualStartTime'));

        return true;
    }


    /**
     * Desc: 自定义规则，验证日参数 字段名要求 字段[start_day, end_day]
     * @param $value
     * @param $rule
     * @param $param
     * @param $title_en
     * @param $title_cn
     * @return true
     * @exception Exception
     * @author lee
     * @date 2024/1/9/10:01
     */
    public function isStartOrEndDay($value, $rule, $param = [], $title_en = '', $title_cn = '')
    {
        // 开始年月日 不能为空
        if (empty($value['start_day']))
            throw new ValidateException(lang('validateStartYearMonthNotNull'));

        // 结束年月日 不能为空
        if (empty($value['end_day']))
            throw new ValidateException(lang('validateEndYearMonthNotNull'));

        // 年月日查询格式错误
        if (!validateDate($value['start_day'], 'Y-m-d') || !validateDate($value['end_day'], 'Y-m-d'))
            throw new ValidateException(lang('validateYearMonthQueryFormatError'));

        // 结束年月日 不能小于 开始年月日
        if (strtotime($value['end_day']) < strtotime($value['start_day']))
            throw new ValidateException(lang('validateEndYearMonthLessThanOrEqualStartTime'));

        return true;
    }

    /**
     * Desc: 二维数组验证处理
     * @param array $data
     * @param array $rules
     * @return bool
     * @throws \Exception
     * @exception Exception
     * @author lee
     * @date 2024/1/9/10:03
     */
    public function checkBatch(array $data, array $rules = []): bool
    {
        $rules = $this->rule ?: $rules;
        foreach ($rules as $key => $rule) {
            if (strpos($key, '.*.') === 0) {
                throw new \Exception('验证规则异常');
            }
            if (strpos($key, '|')) {
                // 字段|描述 用于指定属性名称
                [$key, $title] = explode('|', $key);
            } else {
                $title = $this->field[$key] ?? $key;
            }

            // 场景检测
            if (!empty($this->only) && !in_array($key, $this->only)) {
                continue;
            }

            // 获取数据 支持二维数组
            $value = $this->getBatchDataValue($data, $key);

            // 字段验证
            if ($rule instanceof \Closure) {
                $result = call_user_func_array($rule, [$value, $data]);
            } elseif ($rule instanceof ValidateRule) {
                //  验证因子
                $result = $this->checkItem($key, $value, $rule->getRule(), $data, $rule->getTitle() ?: $title, $rule->getMsg());
            } elseif (strpos($key, '.*.') !== false) {
                $keys = explode('.', $key);
                // 验证数组
                foreach ($value as $item_index => $item_value) {
                    $result = $this->checkItem($key, $item_value, $rule, $data, $title);

                    if (true !== $result) {
                        // 没有返回true 则表示验证失败，结束数组的验证
                        if (!empty($this->batch)) {
                            // 批量验证
                            $this->error[$item_index][end($keys)] = $result;
                        } elseif ($this->failException) {
                            throw new ValidateException($result);
                        } else {
                            $this->error = $result;
                            return false;
                        }
                    }
                }
                $result = true;
            } else {
                $result = $this->checkItem($key, $value, $rule, $data, $title);
            }

            if (true !== $result) {
                // 没有返回true 则表示验证失败
                if (!empty($this->batch)) {
                    // 批量验证
                    $this->error[$key] = $result;
                } elseif ($this->failException) {
                    throw new ValidateException($result);
                } else {
                    $this->error = $result;
                    return false;
                }
            }
        }

        if (!empty($this->error)) {
            if ($this->failException) {
                throw new ValidateException($this->error);
            }
            return false;
        }

        return true;
    }

    /**
     * Desc:获取二维数组参数值
     * @param array $data
     * @param $key
     * @return array|float|int|mixed|string|null
     * @exception Exception
     * @author lee
     * @date 2024/1/9/10:03
     */
    protected function getBatchDataValue(array $data, $key)
    {
        if (is_numeric($key)) {
            $value = $key;
        } elseif (is_string($key) && substr_count($key, '.') === 2) {
            // 支持数组获取数据
            // 获取第一个 . 的位置
            $index = strpos($key, '.');
            // 获取完整的数组数据
            $list = $this->getBatchDataValue($data, substr($key, 0, $index));

            $value = [];
            if (!is_array($list)) {
                // 类型不是数组
                return $list;
            } elseif (strpos($key, '.*.') !== false) {
                // 数组需要提取
                $_key = substr($key, strrpos($key, '.') + 1);
                foreach ($list as $item_key => $item_value) {
                    // 如果不存在该键值也需要赋值一个 null 以确保数据的完整性
                    $value[$item_key] = $item_value[$_key] ?? null;
                }

                unset($_key, $item_key, $item_value);
            } elseif (preg_match_all('/.\d./', $key)) {
                // 提取单个
                $_i = (int)explode('.', $key)[1];

                $_key = substr($key, strrpos($key, '.') + 1);

                $item = $list[$_i] ?? [];

                $value = $item[$_key] ?? null;

                unset($item, $_key, $_i);
            } else {
                throw new ValidateException("参数有误");
            }
        } elseif (is_string($key) && strpos($key, '.')) {
            // 支持多维数组验证
            foreach (explode('.', $key) as $key) {
                if (!isset($data[$key])) {
                    $value = null;
                    break;
                }
                $value = $data = $data[$key];
            }
        } else {
            $value = $data[$key] ?? null;
        }

        return $value;
    }

}