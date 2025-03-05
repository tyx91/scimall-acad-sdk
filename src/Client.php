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
     * 用户登录
     */
    public function login(array $params): array
    {
        return $this->request('login', $params, 'POST');
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
        $getData = array_merge($params, [
            'action' => $action,
            'timestamp' => time()
        ]);

        // 生成签名
        $getData['sign'] = $this->createSign($getData);

        // 发送请求
        $response = $this->sendRequest($this->apiUrl, $getData, $method);
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
     * 发送HTTP请求
     */
    private function sendRequest(string $apiUrl, array $data, string $method = 'GET')
    {
        $method = strtoupper($method);
        if ($method == 'GET') {
            $apiUrl .= '?' . http_build_query($data);
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $apiUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }

        if ($this->debug) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }

        $response = curl_exec($curl);

        if ($response === false) {
            throw new \RuntimeException('CURL请求失败: ' . curl_error($curl));
        }

        curl_close($curl);
        return $response;
    }
}