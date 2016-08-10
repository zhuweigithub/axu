<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    protected $_appId ="";
    protected $_appSecret ="";
    public function __construct()
    {
        $this->_appId = C("APP_ID");
        $this->_appSecret = C("APP_SECRET");
        parent::__construct();
    }
    public function index(){
        $appId = $this->_appId;
        $redirect_url = urlencode("http://www.zhuwei.site/index.php/Home/Index/getList");
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=". $appId ."&redirect_uri=". $redirect_url ."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        header("Location:". $url);
        }
    public function getList(){
        $code = $_GET['code'];
        $get_access_token_url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".  $this->_appId ."&secret=". $this->_appSecret ."&code=". $code ."&grant_type=authorization_code";
        $access_token = json_decode($this->requestUrl('',$get_access_token_url),true);

        $get_user_info_url = "https://api.weixin.qq.com/sns/userinfo?access_token=". $access_token['access_token'] ."&openid=". $access_token['openid'] ."&lang=zh_CN";
        $userInfo = json_decode($this->requestUrl('',$get_user_info_url),true);
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
     * @param $data
     * @param $url
     * @return mixed
     */
    public function requestUrl($data,$url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, []);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_COOKIEJAR, null);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_URL, $url );
        curl_setopt($ch, CURLOPT_POST, true);
        $content  = curl_exec($ch);
        return $content;
    }
}