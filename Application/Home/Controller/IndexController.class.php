<?php
namespace Home\Controller;
use Common\Api\WxApi;
use Think\Controller;
load('Common.WxApi');
class IndexController extends Controller {
    protected $_appId ="";
    protected $_appSecret ="";
    protected $_wxApi = "";
    public function __construct()
    {
     /*   $this->_appId = C("APP_ID");
        $this->_appSecret = C("APP_SECRET");*/
        $this->_appId = "wxd892d9377ca9cabc";
        $this->_appSecret = "99c8d09540dfc387aa4c599fd92819d5";
        $this->_wxApi = new WxApi();
        parent::__construct();
    }
    public function index(){
        $appId = $this->_appId;
        $redirect_url = urlencode("http://www.zhuwei.site/index.php/Home/Index/getList");
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=". $appId ."&redirect_uri=". $redirect_url ."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        header("Location:". $url);
        }
    public function getList(){
        if($_GET['code'] != ''){
            $code = $_GET['code'];
            $oauth = 'https://api.weixin.qq.com/sns/oauth2/access_token';
            $params['appid'] = C('APP_ID');
            $params['secret'] = C('APP_SECRET');
            $params['code'] = $code;
            $params['grant_type'] = 'authorization_code';

            $result = self :: requestUrl($params,$oauth);
            if(!empty($result)){
                $result = json_decode($result);

                $userinfo_url = 'https://api.weixin.qq.com/sns/userinfo';
                unset($params);
                $params['access_token'] = $result->access_token;
                $params['openid'] = $result->openid;
                $params['lang'] = 'zh_CN';
                $userinfo = self :: requestUrl($params , $userinfo_url);
                $userinfo = json_decode($userinfo);
                $unionid = empty($userinfo->unionid) ? $userinfo->unionid : '';

                $data = array(
                 'openid' => $result->openid
                ,'unionid' => $userinfo->unionid
                ,'nickname' => $userinfo->nickname
                ,'sex' => $userinfo->sex
                ,'province' => $userinfo->province
                ,'city' => $userinfo->city
                ,'headimgurl' => $userinfo->headimgurl
                );

                var_dump($data);exit;
              /*  $where = "openid='{$result->openid}' OR unionid={$userinfo->unionid}";
                $user = M('User')->where($where)->find();
                if(!empty($user)){
                    M('User')->save($data);
                }*/

               // header('Location: ' . 'http://' . $_SERVER['HTTP_HOST'] . '?nickname=' . $userinfo->nickname . '&openid=' . $result->openid . '&unionid=' . $unionid);
            }
        }
    }
    public function sss(){
        $vf = session("zw3");
        print_r($vf);
        echo '<br>==============<<<<>>>>';
        $vf1 = session("zw1");
        print_r($vf1);

        exit;
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
    public static function requestUrl($data,$url){
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