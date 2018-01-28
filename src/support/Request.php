<?php

namespace newday\gateway\support;

use newday\gateway\core\traits\InstanceTrait;

class Request
{
    // 实例trait
    use InstanceTrait;

    /**
     * server参数
     *
     * @var array
     */
    protected $server = [];

    /**
     * header参数
     *
     * @var array
     */
    protected $header = [];

    /**
     * param参数
     *
     * @var array
     */
    protected $param = [];

    /**
     * file参数
     *
     * @var array
     */
    protected $file = [];

    /**
     * 输入流
     *
     * @var string
     */
    protected $input;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->autoSetServer();

        $this->autoSetHeader();

        $this->autoSetParam();

        $this->autoSetFile();

        $this->autoSetInput();
    }

    /**
     * 获取server参数
     *
     * @param  string $name
     * @param  string $default
     * @return string|array
     */
    public function server($name = '', $default = null)
    {
        if ('' === $name) {
            return $this->server;
        } else {
            $name = $this->parseName($name);
            return isset($this->server[$name]) ? $this->server[$name] : $default;
        }
    }

    /**
     * 获取header参数
     *
     * @param  string $name
     * @param  string $default
     * @return string|array
     */
    public function header($name = '', $default = null)
    {
        if ('' === $name) {
            return $this->header;
        } else {
            $name = $this->parseName($name);
            return isset($this->header[$name]) ? $this->header[$name] : $default;
        }
    }

    /**
     * 获取param参数
     *
     * @param string $name
     * @param null $default
     * @return string|array
     */
    public function param($name = '', $default = null)
    {
        if ('' === $name) {
            return $this->param;
        } else {
            $name = $this->parseName($name);
            return isset($this->param[$name]) ? $this->param[$name] : $default;
        }
    }

    /**
     * 获取输入流
     *
     * @return string
     */
    public function input()
    {
        return $this->input;
    }

    /**
     * 获取file参数
     *
     * @param string $name
     * @param null $default
     * @return string|array
     */
    public function file($name = '', $default = null)
    {
        if ('' === $name) {
            return $this->file;
        } else {
            $name = $this->parseName($name);
            return isset($this->file[$name]) ? $this->file[$name] : $default;
        }
    }

    /**
     * 自动设置server参数
     */
    protected function autoSetServer()
    {
        $server = [];
        foreach ($_SERVER as $co => $vo) {
            $name = $this->parseName($co);
            $server[$name] = $vo;
        }
        $this->server = $server;
    }

    /**
     * 自动设置header参数
     */
    protected function autoSetHeader()
    {
        $header = [];
        if (function_exists('apache_request_headers') && $result = apache_request_headers()) {
            foreach ($result as $co => $vo) {
                $name = $this->parseName($co);
                $header[$name] = $vo;
            }
        } else {
            $server = $this->server;
            foreach ($server as $key => $val) {
                if (0 === strpos($key, 'http_')) {
                    $key = $this->parseName(substr($key, 5));
                    $header[$key] = $val;
                }
            }
            if (isset($server['CONTENT_TYPE'])) {
                $header['content_type'] = $server['CONTENT_TYPE'];
            }
            if (isset($server['CONTENT_LENGTH'])) {
                $header['content_length'] = $server['CONTENT_LENGTH'];
            }
        }
        $this->header = array_change_key_case($header);
    }

    /**
     * 自动设置param参数
     */
    protected function autoSetParam()
    {
        $param = [];

        foreach ($_GET as $co => $vo) {
            $name = $this->parseName($co);
            $param[$name] = $vo;
        }

        foreach ($_POST as $co => $vo) {
            $name = $this->parseName($co);
            $param[$name] = $vo;
        }

        $this->param = $param;
    }

    /**
     * 自动设置file参数
     */
    protected function autoSetFile()
    {
        $file = [];
        foreach ($_FILES as $co => $vo) {
            $name = $this->parseName($co);
            $file[$name] = $vo;
        }
        $this->file = $file;
    }

    /**
     * 自动设置输入流
     */
    protected function autoSetInput()
    {
        $this->input = file_get_contents('php://input');
    }

    /**
     * 解析键名
     *
     * @param string $name
     * @return string
     */
    protected function parseName($name)
    {
        return str_replace('-', '_', strtolower($name));
    }

}