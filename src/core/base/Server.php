<?php

namespace newday\gateway\core\base;

use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\support\Response;
use newday\gateway\core\traits\ApiResponseTrait;
use newday\gateway\core\traits\ContainerTrait;

class Server
{
    use ContainerTrait;
    use ApiResponseTrait;

    /**
     * 构造响应
     *
     * @param ApiResponse $apiResponse
     * @return Response
     */
    public function buildResponse($apiResponse)
    {
        $responsePack = $this->getResponsePack();
        $data = $apiResponse->getResponse()->toArray();
        $content = $responsePack->pack($data);
        return new Response($content);
    }
}