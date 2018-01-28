<?php

namespace newday\gateway\provider\api;

use newday\gateway\core\api\Api;
use newday\gateway\core\api\ApiRequest;
use newday\gateway\core\objects\IntroObject;
use newday\gateway\provider\ProviderServer;

class BlankApi extends Api
{

    /**
     *
     * {@inheritdoc}
     *
     * @see Api::getIntro()
     */
    public function intro(IntroObject $intro)
    {
        $intro->setName('');
        $intro->setInfo('');
        $intro->setInput([
            'class' => [
                'string',
                '接口类名'
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
        return $server->apiSuccess('请求成功', '');
    }
}