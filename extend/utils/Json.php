<?php
/**
 * Desc Json拓展
 * User 黎笔家
 * Date 2023/12/26
 */

namespace utils;


use think\Response;

class Json
{
    // 默认 请求状态码
    private $code = 200;

    /**
     * 修改请求状态码
     * @param int $code
     * @return $this
     * User 黎笔家
     * Date 2023/12/26
     */
    public function code(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * 创建一个json
     * @param int $code code码
     * @param string $message 提示语
     * @param null $data 数据
     * @param int $code 请求状态码
     * @return Response
     * User 黎笔家
     * Date 2022/13/26
     */
    private function make(int $code, string $message, $data = null): Response
    {
        $res = compact('code', 'message');

        if (!is_null($data)) {
            if (is_array($data) || is_object($data)) $res['data'] = $data;
        }

        return Response::create($res, 'json', $this->code);
    }

    /**
     * 生成 json
     * @param null $data
     * @return Response
     * User 黎笔家
     * Date 2023/9/7
     */
    public function json($data = null, int $code = 0, string $message = ''): Response
    {
        if ($code) $res['code'] = $code;
        if ($message) $res['message'] = $message;

        if (!is_null($data)) {
            if (is_array($data) || is_object($data)) $res['data'] = $data;
        }

        return Response::create($res, 'json', $this->code);
    }

    /**
     * 成功处理
     * @param int $code code码
     * @param string $message 提示语
     * @param null $data 数据
     * @return Response
     * User 黎笔家
     * Date 2023/12/26
     */
    public function success($message = 'ok', int $code = 200, $data = null): Response
    {
        if (is_array($message) || is_object($message)) {
            $data = $message;
            $message = 'ok';
        } elseif ($message === null) {
            $message = 'ok';
        }

        return $this->make($code, $message, $data);
    }

    /**
     * 失败处理
     * @param int $code code码
     * @param string $message 提示语
     * @param null $data 数据
     * @return Response
     * User 黎笔家
     * Date 2023/12/26
     */
    public function fail($message = 'fail', int $code = 4000, $data = null): Response
    {
        if (is_array($message) || is_object($message)) {
            $data = $message;
            $message = 'fail';
        } elseif ($message === null) {
            $message = 'fail';
        }

        return $this->make($code, $message, $data);
    }

    /**
     * 错误处理
     * @param int $code code码
     * @param string $message 提示语
     * @param null $data 数据
     * @return Response
     * User 黎笔家
     * Date 2023/12/26
     */
    public function error($message = 'error', int $code = 5000, $data = null): Response
    {
        if (is_array($message) || is_object($message)) {
            $data = $message;
            $message = 'error';
        } elseif ($message === null) {
            $message = 'error';
        }

        return $this->make($code, $message, $data);
    }

}