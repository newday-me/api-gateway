<?php

namespace newday\gateway\provider\api;

use newday\gateway\core\base\Api;
use newday\gateway\core\api\ApiRequest;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\objects\IntroObject;
use newday\gateway\provider\ProviderServer;

class IntroApi extends Api
{

    /**
     *
     * {@inheritdoc}
     *
     * @see Api::getIntro()
     */
    public function intro(IntroObject $intro)
    {
        $intro->setName(NameConstant::CLASS_API_INTRO);
        $intro->setInfo('获取接口说明');
        $intro->setInput([
            'class' => [
                'type' => 'string',
                'desc' => '接口类名'
            ]
        ]);
        $intro->setOutput([
            'type' => 'object',
            'param' => [
                'class' => [
                    'type' => 'string',
                    'desc' => '接口类名'
                ],
                'name' => [
                    'type' => 'string',
                    'desc' => '接口名'
                ],
                'info' => [
                    'type' => 'string',
                    'desc' => '接口介绍'
                ],
                'input' => [
                    'type' => 'object',
                    'desc' => '接口参数'
                ],
                'output' => [
                    'type' => 'object',
                    'desc' => '接口结果'
                ]
            ]
        ]);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see Api::entry()
     */
    public function entry(ApiRequest $request, ProviderServer $server)
    {
        $class = $request->getParam('api_class');
        if (empty($class)) {
            return $server->apiError('接口类名为空');
        }

        $object = new $class();
        if ($object instanceof Api) {
            $intro = new IntroObject();
            $intro->setClass($class);

            $object->intro($intro);
            return $server->apiSuccess('请求成功', $intro);
        } else {
            return $server->apiError('无效的类名');
        }
    }
}