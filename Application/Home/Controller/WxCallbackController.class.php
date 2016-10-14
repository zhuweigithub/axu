<?php
namespace Home\Controller;
use Think\Controller;
class WxCallbackController extends FatherController{

    private $appId;
    private $appSecret;

    public function __construct(){
        parent::__construct();
        $this->appId = C("APP_ID");
        $this->appSecret = C("APP_SECRET");
    }


    public function generateToken($flush = false) {
        $urlAccessToken = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appId}&secret={$this->appSecret}";
        $token = session('token');
        if ($token && !$flush) {
            return $token['access_token'];
        }

        //没有获取到,或者要求强制刷新
        $token = $this->curl($urlAccessToken);
        if (!$token) {
            throw new Exception("获取token失败");
        }

        //接口返回: {"access_token":"Nu9jllyxOd7YOlhJsRVxiXuaP-fka927pXBspAtfAsgSkySBmd5SAas1ADy7xcrPmQuvcEnZJSyOm2y4fH_NdDG4hLYQVlehzm-1oWrryEA","expires_in":7200}
        $token = json_decode($token, true);

        $token = session('token',$token);//保存token

        return $token['access_token'];
    }

    public function curl($url, $data = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, []);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_COOKIEJAR, null);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $content = curl_exec($ch);

        return $content;
    }

}
