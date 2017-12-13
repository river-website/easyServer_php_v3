<?php
/**
 * Created by PhpStorm.
 * User: win10
 * Date: 2017/12/13
 * Time: 11:14
 */
namespace easyServer\protocol;

use easyServer\connect\tcpConnect;

class httpProtocol extends protocol
{
    public static $methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD', 'OPTIONS');

    public function decode($data,$connect=null)
    {
        $ret= array();
        // TODO: Implement decode() method.
        // Init.
        $ret['HTTP_RAW_POST_DATA'] = '';

        // Parse headers.
        @list($http_header, $http_body) = explode("\r\n\r\n", $data, 2);
        $header_data = explode("\r\n", $http_header);

        @list($ret['REQUEST_METHOD'], $ret['REQUEST_URI'], $ret['SERVER_PROTOCOL']) = explode(' ', $header_data[0]);

        $http_post_boundary = '';
        unset($header_data[0]);
        foreach ($header_data as $content) {
            // \r\n\r\n
            if (empty($content)) continue;

            @list($key, $value)	   = explode(':', $content, 2);
            $key					 = str_replace('-', '_', strtoupper($key));
            $value				   = trim($value);
            $ret['HTTP_' . $key] = $value;
            switch ($key) {
                // HTTP_HOST
                case 'HOST':
                    $tmp					= explode(':', $value);
                    $ret['SERVER_NAME'] = $tmp[0];
                    if (isset($tmp[1])) {
                        $ret['SERVER_PORT'] = $tmp[1];
                    }
                    break;
                // cookie
                case 'COOKIE':
                    parse_str(str_replace('; ', '&', $ret['HTTP_COOKIE']), $_COOKIE);
                    break;
                // content-type
                case 'CONTENT_TYPE':
                    if (!preg_match('/boundary="?(\S+)"?/', $value, $match)) {
                        if ($pos = strpos($value, ';')) {
                            $ret['CONTENT_TYPE'] = substr($value, 0, $pos);
                        } else {
                            $ret['CONTENT_TYPE'] = $value;
                        }
                    } else {
                        $ret['CONTENT_TYPE'] = 'multipart/form-data';
                        $http_post_boundary	  = '--' . $match[1];
                    }
                    break;
                case 'CONTENT_LENGTH':
                    $ret['CONTENT_LENGTH'] = $value;
                    break;
            }
        }

        // Parse $_POST.
        if ($ret['REQUEST_METHOD'] === 'POST') {
            if (isset($ret['CONTENT_TYPE'])) {
                switch ($ret['CONTENT_TYPE']) {
                    case 'multipart/form-data':
                        self::parseUploadFiles($http_body, $http_post_boundary);
                        break;
                    case 'application/x-www-form-urlencoded':
                        parse_str($http_body, $_POST);
                        break;
                    default:
                        // $GLOBALS['HTTP_RAW_POST_DATA']
                        $GLOBALS['HTTP_RAW_REQUEST_DATA'] = $GLOBALS['HTTP_RAW_POST_DATA'] = $http_body;
                }
            } else {
                // $GLOBALS['HTTP_RAW_POST_DATA']
                $GLOBALS['HTTP_RAW_REQUEST_DATA'] = $GLOBALS['HTTP_RAW_POST_DATA'] = $http_body;
            }
        }

        if ($ret['REQUEST_METHOD'] === 'PUT') {
            $GLOBALS['HTTP_RAW_REQUEST_DATA'] = $http_body;
        }

        if ($ret['REQUEST_METHOD'] === 'DELETE') {
            $GLOBALS['HTTP_RAW_REQUEST_DATA'] = $http_body;
        }

        // QUERY_STRING
        $ret['QUERY_STRING'] = parse_url($ret['REQUEST_URI'], PHP_URL_QUERY);
        if ($ret['QUERY_STRING']) {
            // $GET
            parse_str($ret['QUERY_STRING'], $_GET);
        } else {
            $ret['QUERY_STRING'] = '';
        }

        // REQUEST
        $_REQUEST = array_merge($_GET, $_POST);

        // REMOTE_ADDR REMOTE_PORT
        $ret['REMOTE_ADDR'] = $connect->getRemoteIp();
        $ret['REMOTE_PORT'] = $connect->getRemotePort();

        return $ret;
    }
    public function encode($data)
    {
        if (empty($header)){
            $header = "HTTP/1.1 200 OK\r\n";
            $header .= "Content-Type: text/html;charset=utf-8\r\n";
            $header .= "Server: easy/" . "easy server" . "\r\nContent-Length: " . strlen($data) . "\r\n\r\n";
        }
        return $header . $data;

        // TODO: Implement encode() method.
        // Default http-code.
        if (!isset(HttpCache::$header['Http-Code'])) {
            $header = "HTTP/1.1 200 OK\r\n";
        } else {
            $header = HttpCache::$header['Http-Code'] . "\r\n";
            unset(HttpCache::$header['Http-Code']);
        }

        // Content-Type
        if (!isset(HttpCache::$header['Content-Type'])) {
            $header .= "Content-Type: text/html;charset=utf-8\r\n";
        }

        // other headers
        foreach (HttpCache::$header as $key => $item) {
            if ('Set-Cookie' === $key && is_array($item)) {
                foreach ($item as $it) {
                    $header .= $it . "\r\n";
                }
            } else {
                $header .= $item . "\r\n";
            }
        }

        // header
        $header .= "Server: easy/" . "easy server" . "\r\nContent-Length: " . strlen($content) . "\r\n\r\n";

        // save session
        self::sessionWriteClose();

        // the whole http package
        return $header . $content;
    }
    public function getInfo($data)
    {
        // TODO: Implement getInfo() method.
        if (!strpos($data, "\r\n\r\n")) {
            // Judge whether the package length exceeds the limit.
            if (strlen($data) >= tcpConnect::$maxPackageSize) {
                return false;
            }
            return 0;
        }

        list($header,) = explode("\r\n\r\n", $data, 2);
        $method = substr($header, 0, strpos($header, ' '));

        if(in_array($method, static::$methods)) {
            return static::getRequestSize($header, $method);
        }else{
            return false;
        }
    }
    private static function getRequestSize($header, $method)
    {
        if($method === 'GET' || $method === 'OPTIONS' || $method === 'HEAD') {
            return strlen($header) + 4;
        }
        $match = array();
        if (preg_match("/\r\nContent-Length: ?(\d+)/i", $header, $match)) {
            $content_length = isset($match[1]) ? $match[1] : 0;
            return $content_length + strlen($header) + 4;
        }
        return 0;
    }
}

class requestData{
    public $GET = [];                          # get 请求参数
    public $POST = [];                         # post 请求参数
    public $REQUEST = [];                      # 请求参数
    public $COOKIE = [];                        # cookie
    public $CONTENT_TYPE = null;                 # 内容类型public $
    public $QUERY_STRING = null;                 # 未解析的原始请求字符串
    public $REQUEST_METHOD = null;               # 请求方式
    public $REQUEST_URI = null;                  # 当前URL的 路径地址
    public $SERVER_PROTOCOL = null;              # 请求页面时通信协议的名称和版本
    public $SERVER_SOFTWARE = null;             # 服务器标识的字串
    public $SERVER_NAME = null;                  # 服务器主机的名称
    public $HTTP_HOST = null;                    # 当前请求的 Host: 头部的内容
    public $HTTP_USER_AGENT = null;              # 当前请求的 User_Agent: 头部的内容
    public $HTTP_ACCEPT = null;                  # 当前请求的 Accept: 头部的内容
    public $HTTP_ACCEPT_LANGUAGE = null;         # 浏览器语言
    public $HTTP_ACCEPT_ENCODING = null;         # 当前请求的 Accept-Encoding: 头部的内容
    public $HTTP_COOKIE = null;                  # cookie
    public $HTTP_CONNECTION = null;              # 当前请求的 Connection: 头部的内容。例如：“Keep-Alive”
    public $REMOTE_ADDR = null;                  # 当前用户 IP
    public $REMOTE_HOST = null;                  # 当前用户主机名
    public $REMOTE_PORT = null;                  # 端口
    public $HTTP_RAW_POST_DATA  = null;          # 请求数据
    public $REQUEST_TIME = 0;
    public function __construct()
    {
        $this->REQUEST_TIME = time();
        $this->SERVER_SOFTWARE = 'easy server';
    }
}

class HttpCache
{
    public static $codes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
    );

    /**
     * @var HttpCache
     */
    public static $instance			 = null;
    public static $header			   = array();
    public static $sessionPath		  = '';
    public static $sessionName		  = '';
    public static $sessionGcProbability = 1;
    public static $sessionGcDivisor	 = 1000;
    public static $sessionGcMaxLifeTime = 1440;
    public $sessionStarted = false;
    public $sessionFile = '';

    public static function init()
    {
        self::$sessionName = ini_get('session.name');
        self::$sessionPath = @session_save_path();
        if (!self::$sessionPath || strpos(self::$sessionPath, 'tcp://') === 0) {
            self::$sessionPath = sys_get_temp_dir();
        }

        if ($gc_probability = ini_get('session.gc_probability')) {
            self::$sessionGcProbability = $gc_probability;
        }

        if ($gc_divisor = ini_get('session.gc_divisor')) {
            self::$sessionGcDivisor = $gc_divisor;
        }

        if ($gc_max_life_time = ini_get('session.gc_maxlifetime')) {
            self::$sessionGcMaxLifeTime = $gc_max_life_time;
        }

        @\session_start();
    }
}
