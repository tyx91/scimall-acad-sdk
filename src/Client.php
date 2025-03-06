<?php
namespace Scimall\Acad;
class Client
{
    private $appKey;
    private $apiUrl;
    private $debug = false;

    public function __construct(string $appKey, string $apiUrl)
    {
        $this->appKey = $appKey;
        $this->apiUrl = $apiUrl;
    }

    public function setDebug(bool $debug): void
    {
        $this->debug = $debug;
    }


    /**
     * 获取用户信息
     */
    public function getUserInfo(array $params): array
    {
        return $this->request('get_user_info', $params);
    }

    /**
     * 检查是否是会员
     */
    public function isMember(array $params): array
    {
        return $this->request('is_member', $params);
    }

    /**
     * 检查是否是O会员
     */
    public function isOmember(array $params): array
    {
        return $this->request('is_omember', $params);
    }

    /**
     * 检查会员信息
     */
    public function checkMember(array $params): array
    {
        return $this->request('check_member', $params);
    }

    /**
     * 检查单位会员信息
     */
    public function checkUnitMember(array $params): array
    {
        return $this->request('check_unit_member', $params);
    }

    /**
     * 获取用户身份信息
     */
    public function getUserIdentity(array $params): array
    {
        return $this->request('user_identity', $params);
    }

    /**
     * 发送请求
     */
    private function request(string $action, array $params, string $method = 'GET'): array
    {
        $getData = [
            'action' => $action,
            'timestamp' => time()
        ];
        if(strtoupper($method)=='GET'){
            $getData = array_merge($getData,$params);
        }
        //生成签名
        $getData['sign'] = $this->createSign($getData);
        // 发送请求
        $response = $this->sendRequest($this->apiUrl, $getData, $params, $method);
        return json_decode($response, true);
    }

    /**
     * 生成签名
     */
    private function createSign(array $getData): string
    {
        $queryStr = md5(http_build_query($getData));
        return md5($queryStr . $this->appKey);
    }

    /**
     * 发送接口请求（支持GET/POST）
     * @Author : Yasin
     *
     * @param string $url 接口地址（需包含基础路径）
     * @param array $getData GET请求数据
     * @param array $postData POST请求数据，空数组表示不使用POST
     * @param string $method 请求方法，GET或POST
     *
     * @return mixed 接口返回的原始数据（建议JSON解码后使用）
     */
    public function sendRequest(string $apiUrl, array $getData, array $postData = [], string $method = 'GET')
    {
        $method = strtoupper($method);
        // 构建GET请求URL
        $apiUrl .= '?'.http_build_query($getData);
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $apiUrl);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //设置post方式提交
        if ($method == 'POST' && !empty($postData)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
        }
        $data = curl_exec($curl);

        // 新增执行过程的错误处理说明
        if ($data === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new RuntimeException('CURL请求失败: '.$error);
        }

        curl_close($curl);
        return $data;
    }
}