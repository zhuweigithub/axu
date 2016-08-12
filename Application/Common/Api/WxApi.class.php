<?php
namespace Common\Api;
class WxApi{

    protected $_appId ="";
    protected $_appSecret ="";
    public function __construct()
    {
        $this->_appId = C("APP_ID");
        $this->_appSecret = C("APP_SECRET");
    }
    public function getList( $code ){
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->_appId&secret=$this->_appSecret";
        $token = $this->getJson($url);
        //第二步:取得openid
        $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$this->_appId&secret=$this->_appSecret&code=$code&grant_type=authorization_code";
        $oauth2 = $this->getJson($oauth2Url);

        session("zw1",$oauth2);exit;
        $_SESSION['access_token']=$token["access_token"];
        //第三步:根据全局access_token和openid查询用户信息
        $access_token = $token["access_token"];
        $openid = $oauth2['openid'];

        $get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
        $userInfo = $this->getJson($get_user_info_url);
        session("zw",$userInfo);
    }
    public function sss(){
        $vf = session("zw");
        print_r($vf);exit;
    }
    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = "zhuwei";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            $echoStr = $_GET["echostr"];
            echo $echoStr;
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $url
     * @return mixed
     */
    private  function getJson($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }
}