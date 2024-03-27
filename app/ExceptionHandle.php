<?php

namespace app;

use BadMethodCallException;
use exception\AuthException;
use exception\ParamsException;
use exception\ThrottleException;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\db\exception\PDOException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Lang;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */
    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        $respData = [];
        $debug = env('APP_DEBUG');
        if ($debug) $respData['trace'] = $e->getTrace();

        // 异常处理机制
        // 验证器类异常 或 参数类异常
        if ($e instanceof ValidateException || $e instanceof ParamsException) {
            return app('json')->fail(\lang($e->getMessage()), $e->getCode() ?: 4022);
        }

        // 自定义异常
        if ($e instanceof ThrottleException) {
            return app('json')->fail(\lang($e->getMessage()), $e->getCode() ?: 4000);
        }

        // 身份验证异常
        if ($e instanceof AuthException) {
            if ($request->isPost() || $request->isAjax()) {
                return app('json')->error(\lang($e->getMessage()), $e->getCode() ?: 5000, $respData);
            }
        }

        // 宏异常
        if ($e instanceof BadMethodCallException) {
            // 判断宏建立的用户字段是否存在，不存在则登录失效
            $check = null;
            if (strstr($e->getMessage(), 'user_id')) $check = true;
            if (strstr($e->getMessage(), 'user_name')) $check = true;

            return app('json')->error($check ? \lang('loginHasExpired') : \lang($e->getMessage()), $e->getCode() ?: 5000, $respData);
        }

        // 记录错误信息
        // 线上不抛未知异常
        if (!$debug) {
            $remark_code = app('snowflake')->createId('FAIL');
            tryErrorLog($e, 'select', $remark_code, 'exception');
            return app('json')->error('服务器异常，请联系客服人员。错误关键码：' . $remark_code, 5000);
        }

        // 数据库异常
        if ($e instanceof PDOException) {
            if (strpos($e->getMessage(), 'Integrity constraint violation') !== false) {
                return app('json')->error(\lang('validateMysqlUnq'), 5000, $respData);
            }
        }

        // 其他错误交给系统处理
        return parent::render($request, $e);
    }
}
