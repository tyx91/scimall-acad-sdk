<?php
require __DIR__.'/../vendor/autoload.php';
use Scimall\Acad\Client;
// 配置参数
$appKey ='5523c186';
$apiUrl = 'http://tyx.acads_sy.scimall.vip/open-api/api.php';
// 初始化客户端
$client = new Client($appKey, $apiUrl);
try {
    // 示例1：获取用户身份信息
    $params = [
        'phone' => '13548578246',
        'email' => 'test@example.com'
    ];
    $result = $client->getUserIdentity($params);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage();
}

print_r($result);die;