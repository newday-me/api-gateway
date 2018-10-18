<?php

namespace newday\gateway\core\base;

use newday\gateway\core\support\Http;
use newday\gateway\core\support\Request;
use newday\gateway\core\support\Signature;
use newday\gateway\core\pack\RequestPack;
use newday\gateway\core\pack\ResponsePack;
use newday\gateway\core\interfaces\HttpInterface;
use newday\gateway\core\interfaces\PackInterface;
use newday\gateway\core\interfaces\RequestInterface;
use newday\gateway\core\interfaces\SignatureInterface;

/**
 * Class Application
 * @package newday\gateway\core\base
 *
 * @property HttpInterface $http
 * @property RequestInterface $request
 * @property SignatureInterface $signature
 * @property PackInterface $requestPack
 * @property PackInterface $responsePack
 */
class Container
{
    /**
     * 模块对象
     *
     * @var array
     */
    protected $modules = [];

    /**
     * 类映射
     *
     * @var array
     */
    protected $classes = [];

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->_initialize();
    }

    /**
     * 初始化
     */
    protected function _initialize()
    {
        $this->registerClassMulti([
            'http' => Http::class,
            'request' => Request::class,
            'signature' => Signature::class,
            'request_pack' => RequestPack::class,
            'response_pack' => ResponsePack::class
        ]);
    }

    /**
     * 批量注册类
     *
     * @param array $list
     */
    public function registerClassMulti($list)
    {
        foreach ($list as $key => $value) {
            $this->registerClass($key, $value);
        }
    }

    /**
     * 注册类
     *
     * @param string $name
     * @param string $class
     */
    public function registerClass($name, $class)
    {
        $name = $this->processName($name);
        $this->classes[$name] = $class;
        unset($this->modules[$name]);
    }

    /**
     * 注册对象
     *
     * @param string $name
     * @param mixed $module
     */
    public function register($name, $module)
    {
        $name = $this->processName($name);
        $this->injectContainer($module);
        $this->modules[$name] = $module;
    }

    /**
     * 获取模块对象
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        try {
            $name = $this->processName($name);
            if (!isset($this->modules[$name])) {
                if (!isset($this->classes[$name])) {
                    return null;
                }
                $class = $this->classes[$name];

                $module = new $class();
                $this->register($name, $module);
            }
            return $this->modules[$name];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * 注入容器
     *
     * @param mixed $module
     */
    protected function injectContainer($module)
    {
        $method = 'injectContainer';
        if (!$module instanceof Container && method_exists($module, $method)) {
            $module->$method($this);
        }
    }

    /**
     * 处理名称
     *
     * @param string $name
     * @return string
     */
    private function processName($name)
    {
        return strtolower(str_replace('_', '', $name));
    }
}