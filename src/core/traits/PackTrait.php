<?php

namespace newday\gateway\core\traits;

use newday\gateway\core\pack\RequestPack;
use newday\gateway\core\pack\ResponsePack;

trait PackTrait
{
    /**
     * 请求打包对象
     *
     * @var RequestPack
     */
    protected $requestPack;

    /**
     * 回复打包对象
     *
     * @var ResponsePack
     */
    protected $responsePack;

    /**
     * 获取请求打包对象
     *
     * @return RequestPack
     */
    public function getRequestPack()
    {
        if (is_null($this->requestPack)) {
            $this->requestPack = RequestPack::getInstance();
        }
        return $this->requestPack;
    }

    /**
     * 设置请求打包对象
     *
     * @param $requestPack
     */
    public function setRequestPack($requestPack)
    {
        $this->requestPack = $requestPack;
    }

    /**
     * 获取回复打包对象
     *
     * @return ResponsePack
     */
    public function getResponsePack()
    {
        if (is_null($this->responsePack)) {
            $this->responsePack = ResponsePack::getInstance();
        }
        return $this->responsePack;
    }

    /**
     * 设置回复打包对象
     *
     * @param ResponsePack $responsePack
     */
    public function setResponsePack($responsePack)
    {
        $this->responsePack = $responsePack;
    }
}