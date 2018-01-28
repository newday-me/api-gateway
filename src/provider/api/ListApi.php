<?php

namespace newday\gateway\provider\api;

use newday\gateway\core\api\Api;
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