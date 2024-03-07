<?php
/**
 * Desc: 跨域中间件
 * User: 黎笔家
 * Date-Time: 2024/3/7/15:26
 */

namespace http\middleware;

use app\Request;
use http\interfaces\MiddlewareInterface;
use think\facade\Config;
use think\Response;

class AllowOriginMiddleware implements MiddlewareInterface
{
    /**
     * header头
     * @var string[]
     */
    protected $header = [
        'Access-Control-Allow-Origin'  => '*',
        'Access-Control-Allow-Headers' => 'Authori-zation, Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With, businessid, SubSystemId, think-lang, warehouse-code',
        'Access-Control-Allow-Methods' => 'GET,POST,PATCH,PUT,DELETE,OPTIONS,DELETE',
        'Access-Control-Max-Age'       => '1728000'
    ];

    /**
     * 允许跨域的域名
     * @var
     */
    protected $cookieDomain;

    /**
     * Desc: 跨域设置
     * @param Request $request
     * @param \Closure $next
     * @return Response
     * @exception Exception
     * @author lee
     * @date 2024/3/7/15:28
     */
    public function handle(Request $request, \Closure $next)
    {
        $this->cookieDomain = Config::get('cookie.domain', '');
        $header = $this->header;
        $origin = $request->header('origin');

        if ($origin && ('' != $this->cookieDomain && strpos($origin, $this->cookieDomain)))
            $header['Access-Control-Allow-Origin'] = $origin;

        if ($request->method(true) == 'OPTIONS') {
            $response = Response::create('ok')->code(200)->header($header);
        } else {
            $response = $next($request)->header($header);
        }
        $request->filter(['htmlspecialchars', 'strip_tags', 'addslashes', 'trim']);
        return $response;
    }
}