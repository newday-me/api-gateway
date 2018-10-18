<?php

namespace newday\gateway\provider\api;

use newday\gateway\core\base\Api;
use newday\gateway\core\api\ApiRequest;
use newday\gateway\core\constant\NameConstant;
use newday\gateway\core\objects\IntroObject;
use newday\gateway\provider\ProviderServer;

class ListApi extends Api
{
    /**
     *
     * {@inheritdoc}
     *
     * @see Api::getIntro()
     */
    public function intro(IntroObject $intro)
    {
        $intro->setName(NameConstant::CLASS_API_LIST);
        $intro->setInfo('获取接口列表');
        $intro->setInput([]);
        $intro->setOutput([
            'type' => 'object',
            'param' => [
                'list' => [
                    'type' => 'array',
                    'desc' => '接口列表',
                    'item' => [
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
                    ]
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
        $apiList = $server->getApiList();
        $list = [];
        foreach ($apiList as $class) {
            $object = new $class();
            if ($object instanceof Api) {
                $intro = new IntroObject();
                $intro->setClass($class);

                $object->intro($intro);
                $list[] = $intro;
            }
        }

        $data = [
            'list' => $list
        ];
        return $server->apiSuccess('操作成功', $data);
    }

}