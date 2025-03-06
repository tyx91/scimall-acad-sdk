<?php
require __DIR__.'/../vendor/autoload.php';
use Scimall\Acad\Client;
// 配置参数
$appKey ='xxxxxxx';
$apiUrl = '联系管理员';
// 初始化客户端
$client = new Client($appKey, $apiUrl);
try {
    // 示例1：获取用户身份信息
    $params = [
        'phone' => 'XXXXX',
        'email' => 'test@example.com'
    ];
    $result = $client->getUserIdentity($params);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage();
}

print_r($result);die;