<?php
declare (strict_types = 1);

/**
 * Http请求
 */

namespace utils;


class Http
{

    /*
     * Content-Type:
    常见的媒体格式类型如下：
    text/html ： HTML格式
    text/plain ：纯文本格式
    text/xml ：  XML格式
    image/gif ：gif图片格式
    image/jpeg ：jpg图片格式
    image/png：png图片格式

    以application开头的媒体格式类型：
    application/xhtml+xml ：XHTML格式
    application/xml     ： XML数据格式
    application/atom+xml  ：Atom XML聚合格式
    application/json    ： JSON数据格式
    application/pdf       ：pdf格式
    application/msword  ： Word文档格式
    application/octet-stream ： 二进制流数据（如常见的文件下载）
    application/x-www-form-urlencoded ： form表单数据被编码为key/value格式发送到服务器（表单默认的提交数据的格式）

    另外一种常见的媒体格式是上传文件之时使用的：
    multipart/form-data ： 需要在表单中进行文件上传时，就需要使用该格式
     * */

    public $url = '';
    public $queryString = '';
    public $headers = ["Content-Type: application/json; charset=UTF-8"];
    public $timeout = 3000;
    public $userAgent = '';

    const HTTP_PUT = 'put';
    const HTTP_DELETE = "DELETE";
    const HTTP_PATCH = 'PATCH';
    const HTTP_HEAD = 'HEAD';
    const HTTP_OPTIONS = 'OPTIONS';
    const HTTP_TRACE = 'TRACE';
    const HTTP_MOVE = 'MOVE';
    const HTTP_COPY = 'COPY';
    const HTTP_LINK = 'LINK';
    const HTTP_UNLINK = 'UNLINK';
    const HTTP_WRAPPED = 'WRAPPED';

    //组装数据
    public function setData($params)
    {
        $fixedParams = array();
        foreach ($params as $k => $v) {
            if (gettype($v) != "string") {
                $fixedParams += [$k => json_encode($v)];
            } else {
                $fixedParams += [$k => $v];
            }
        }

        $str_to_digest = "";
        foreach ($fixedParams as $k => $v) {
            $str_to_digest = $str_to_digest . $k . "=" . $v . "&";
        }

        $this->queryString = substr($str_to_digest, 0, -1);

        return $this;
    }

    //json字符串
    public function setArrData($data)
    {
        $this->queryString = json_encode($data);
        return $this;
    }

    //jason字符串（中文不转码)
    public function setArrDataUnioncode($data){
        $this->queryString=json_encode($data,JSON_UNESCAPED_UNICODE);
        // dump($this->queryString);exit;
        return $this;
    }

    public function setJsonData($json_str)
    {
        $this->queryString = $json_str;
        return $this;
    }

    public function setHttpBuildQuery($data)
    {
        $this->queryString = http_build_query($data);
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function handleUrl()
    {
        $this->url = $this->url . '?' . $this->queryString;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function setTimeOut($time)
    {
        $this->timeout = $time;
        return $this;
    }

    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    //普通get请求
    public function get()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        if (strpos($this->url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($ch, CURLOPT_HEADER, 0); //设置头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers); // 设置请求头
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout); // 超时设置,以秒为单位
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeout);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $response = json_encode(['code' => 4000, 'message' => curl_error($ch)]);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//                $response = json_encode(['code' => $httpStatusCode, 'message' => $response]);
            if (200 !== $httpStatusCode) {
                $response = json_encode(['code' => 4000, 'message' => $response]);
            }
        }

        curl_close($ch);
        return $response;
    }

    //普通post请求
    public function post()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url); //设置链接
        if (strpos($this->url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if ($this->userAgent) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设置是否返回信息
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers); //设置HTTP头
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeout);
        curl_setopt($ch, CURLOPT_POST, 1); //设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->queryString);
        $response = curl_exec($ch);
//dump($this);
        if (curl_errno($ch)) {
            $response = json_encode(['code' => 4000, 'message' => curl_error($ch)]);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//                $response = json_encode(['code' => $httpStatusCode, 'message' => $response]);

            if (200 !== $httpStatusCode) {

                $response = json_encode(['code' => 4000, 'message' => $response]);

            }
        }

        curl_close($ch);

        return $response;
    }

    //request Payload参数的请求
    //$json：json字符串
    public function payload()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url); //设置链接
        if (strpos($this->url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if ($this->userAgent) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //设置是否返回信息
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers); //设置HTTP头
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); //设置为POST方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->queryString);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $message = curl_error($ch);
            if (!$message) {
                $message = $this->error_codes[curl_errno($ch)];
            }

            $response = json_encode(['code' => 4000, 'message' => $message]);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//                $response = json_encode(['code' => $httpStatusCode, 'message' => $response]);
            if (200 !== $httpStatusCode) {
                $response = json_encode(['code' => 4000, 'message' => $response]);
            }
        }
        curl_close($ch);
        return $response;
    }

    //其他类型请求
    public function omethod($method)
    {
        $method = strtoupper($method);
        switch ($method) {
            case "PUT":
                $this->headers[] = "X-HTTP-Method-Override: PUT";
                break;
            case "DELETE":
                $this->headers[] = "X-HTTP-Method-Override: DELETE";
                $this->url .= '?' . $this->queryString;
                break;
            default:
                break;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        if (strpos($this->url, 'https') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        if ($this->userAgent) {
            curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->queryString);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $response = json_encode(['code' => 4000, 'message' => curl_error($ch)]);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//                $response = json_encode(['code' => $httpStatusCode, 'message' => $response]);
            $response = json_encode(['code' => 4000, 'message' => $response]);

        }
        curl_close($ch);
        return $response;
    }

}
