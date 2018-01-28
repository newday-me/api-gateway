<?php

namespace newday\gateway\core\api;

use newday\gateway\core\objects\IntroObject;
use newday\gateway\provider\ProviderServer;

abstract class Api
{

    /**
     * 接口介绍
     *
     * @param  IntroObject $intro
     */
    abstract public function intro(IntroObject $intro);

    /**
     * 接口入口
     *
     * @param ApiRequest $request
     * @param ProviderServer $server
     * @return ApiResponse
     */
    abstract public function entry(ApiRequest $request, ProviderServer $server);

}