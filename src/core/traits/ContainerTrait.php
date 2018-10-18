<?php

namespace newday\gateway\core\traits;

use newday\gateway\core\base\Container;
use newday\gateway\core\interfaces\HttpInterface;
use newday\gateway\core\interfaces\PackInterface;
use newday\gateway\core\interfaces\RequestInterface;
use newday\gateway\core\interfaces\SignatureInterface;

trait ContainerTrait
{
    /**
     * 容器
     *
     * @var Container
     */
    protected $container;

    /**
     * 设置容器
     *
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * 获取容器
     *
     * @return Container
     */
    protected function getContainer()
    {
        if (is_null($this->container)) {
            $this->container = new Container();
        }
        return $this->container;
    }

    /**
     * 获取Http对象
     *
     * @return HttpInterface
     */
    protected function getHttp()
    {
        return $this->getContainer()->http;
    }

    /**
     * 获取请求对象
     *
     * @return RequestInterface
     */
    protected function getRequest()
    {
        return $this->getContainer()->request;
    }

    /**
     * 获取签名对象
     *
     * @return SignatureInterface
     */
    protected function getSignature()
    {
        return $this->getContainer()->signature;
    }

    /**
     * 获取请求打包对象
     *
     * @return PackInterface
     */
    protected function getRequestPack()
    {
        return $this->getContainer()->requestPack;
    }

    /**
     * 获取响应打包对象
     *
     * @return PackInterface
     */
    protected function getResponsePack()
    {
        return $this->getContainer()->responsePack;
    }
}