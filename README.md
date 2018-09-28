API网关
-------

统一的API服务，适用于微服务的搭建。

客户端
-------

```php
$config = new HubConfig();
$config->setAppKey('app_key_xx');
$config->setAppToken('app_token_xx');
$config->setServerUrl('http://api.xxx.com/gateway/hub/server');

// 负载均衡
$config->setServerIpListStr("192.168.10.1|100
192.168.10.2|100");

// 请求头
$config->setServerHeader([
    'header-xx' => 'value-xx'
]);

$client = new HubClient($config);
$apiName = 'ap_name_xx';
$data = ['param_xx' => 'value_xx'];
$apiResponse = $client->request($apiName, $data);
$response = $apiResponse->getResponse();
if($response->isSuccess()){
    // todo
}
echo 'code:'.$response->getCode().'<br/>';
echo 'code:'.$response->getMsg().'<br/>';
echo 'data:<pre>'.var_export($response->getData(), true).'</pre>';
echo 'extra:<pre>'.var_export($response->getExtra(), true).'</pre>';
```

网关
-------

网关地址：http://api.xxx.com/gateway/hub/server
```
use newday\gateway\hub\HubServer as BaseHubServer;

class HubServer extends BaseHubServer
{

    /**
     * 获取应用令牌
     *
     * @param string $appKey
     * @return string
     * @throws ServerException
     */
    protected function getAppToken($appKey)
    {
        if (empty($appKey)) {
            throw new ServerException('应用密钥为空');
        }

        $appList = [
            'app_key_xx' => 'app_token_xx'
        ];
        if(!isset($appList[$appKey])){
            throw new ServerException('应用不存在');
        }

        return $appList[$appKey];
    }

    /**
     * 获取接口类名
     *
     * @param string $appKey
     * @param string $apiName
     * @param ProviderConfig $config
     * @return string
     * @throws ServerException
     */
    protected function getApiClass($appKey, $apiName, ProviderConfig $config)
    {
        if (empty($apiName)) {
            throw new ServerException('接口名称为空');
        }

        $authList = [
            'app_key_xx' => [
                'ap_name_xx'
            ]
        ];
        if(!isset($authList[$appKey]) || !in_array($apiName, $authList[$appKey])){
            throw new ServerException('没有接口权限');
        }

        $apiList = [
            'ap_name_xx' => [
                'api_class' => 'app\\module\\api\\DemoApi',
                'server_name' => 'server_xx'
            ]
        ];
        if(!isset($apiList[$apiName])){
            throw new ServerException('接口不存在');
        }
        $api = $apiList[$apiName];

        $serverList = [
            'server_xx' => [
                'server_url' => 'http://www.xxx.com/gateway/provider/server',
                'server_ip' => '172.16.0.1|100',
                'server_token' => 'server_token_xx'
            ]
        ];
        $serverName = $api['server_name'];
        if(!isset($serverList[$serverName])){
            throw new ServerException('服务不存在');
        }
        $server = $serverList[$serverName];

        // 配置
        $config->setServerToken($server['server_token']);
        $config->setServerUrl($server['server_url']);
        $config->setServerIpListStr($server['server_ip']);

        return $api['api_class'];
    }
}

$server = new HubServer();
$apiResponse = $server->entry();
$apiResponse->send();
```

服务端
-------

服务地址：http://www.xxx.com/gateway/provider/server
```
namespace app\module\api;

class  DemoApi extends Api{

    /**
     * 接口介绍
     *
     * @param  IntroObject $intro
     */
    public function intro(IntroObject $intro)
    {
        $intro->setName('api_name_xx');
        $intro->setInfo('测试API');
        $intro->setInput([
            'param_xx' => [
                'string',
                '变量xx'
            ]
        ]);
    }

    /**
     * 接口入口
     *
     * @param ApiRequest $request
     * @param ProviderServer $server
     * @return ApiResponse
     */
    public function entry(ApiRequest $request, ProviderServer $server)
    {
        $paramXx = $request->getParam('param_xx');
        if(empty($paramXx)){
            return $server->apiError('变量xx为空', [
                'param_xx' => 'value_xx'
            ]);
        }
        else{
            return $server->apiSuccess('获取成功', [
                'param_xx' => 'value_xx'
            ]);
        }
    }
}
```
```
use app\module\api\DemoApi;

// 服务端配置
$config = new ProviderConfig();
$config->setServerToken('server_token_xx');
$server = new ProviderServer($config);

// 注册接口
$server->registerApi([
    DemoApi::class
]);

$apiResponse = $server->entry();
$apiResponse->send();
```