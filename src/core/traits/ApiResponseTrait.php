<?php

namespace newday\gateway\core\traits;

use newday\gateway\core\api\ApiResponse;
use newday\gateway\core\constant\CodeConstant;
use newday\gateway\core\objects\ResponseObject;

trait ApiResponseTrait
{
    /**
     * 接口成功
     *
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @return ApiResponse
     */
    public function apiSuccess($msg = 'success', $data = '', $extra = [])
    {
        return $this->apiData(CodeConstant::API_SUCCESS, $msg, $data, $extra);
    }

    /**
     * 接口失败
     *
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @return ApiResponse
     */
    public function apiError($msg = 'error', $data = '', $extra = [])
    {
        return $this->apiData(CodeConstant::API_ERROR, $msg, $data, $extra);
    }

    /**
     * 接口数据
     *
     * @param $code
     * @param string $msg
     * @param string $data
     * @param array $extra
     * @return ApiResponse
     */
    public function apiData($code, $msg = 'error', $data = '', $extra = [])
    {
        $responseObject = new ResponseObject();
        $responseObject->setCode($code);
        $responseObject->setMsg($msg);
        $responseObject->setData($data);
        $responseObject->setExtra($extra);
        return new ApiResponse($responseObject);
    }
}