<?php
namespace Home\Controller;
use Common\Api\WxApi;
use Think\Controller;
load('Common.WxApi');
class IndexController extends FatherController {
    protected $_appId ="";
    protected $_appSecret ="";
    protected $_wxApi = "";
    public function __construct()
    {
        $this->_appId = C("APP_ID");
        $this->_appSecret = C("APP_SECRET");
        $this->_wxApi = new WxApi();
        parent::__construct();

    }
    
    public function index(){

           /* $arr['nickname'] = "111";
            $arr['openid'] = "ovrgAv3nbdTq4r_tHZePpz3tLvlw";
            $arr['buyer_img'] = "http://wx.qlogo.cn/mmopen/e5T4Ra5arzSE5T0pibQMlNnRMNsGcrU1x5oD3YTlrA995dwFeCIlGBpfLUSj08jR21J0FbXH3lFBibZAU4obySqiccUJSbtY6cE";*/
            //session('userInfo',$arr,7200*24);
           if(!$this->getUserInfo()){
               $appId = $this->_appId;
               $redirect_url = urlencode("http://www.zhuwei.site/index.php/Home/Index/getList");
               $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=". $appId ."&redirect_uri=". $redirect_url ."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
               header("Location:". $url);
            }else{
                $this->indexList();
            }
        }
    public function getList(){
        if($_GET['code'] != ''){
            $code = $_GET['code'];
            $oauth = 'https://api.weixin.qq.com/sns/oauth2/access_token';
            $params['appid'] = $this->_appId;
            $params['secret'] = $this->_appSecret;
            $params['code'] = $code;
            $params['grant_type'] = 'authorization_code';

            $result = http( $oauth , $params );
            if(!empty($result)){
                $result = json_decode($result);
                session("access_token",$result->access_token);
                session("openid",$result->openid);
                $userinfo_url = 'https://api.weixin.qq.com/sns/userinfo';
                unset($params);
                $params['access_token'] = $result->access_token;
                $params['openid'] = $result->openid;
                $params['lang'] = 'zh_CN';
                $userinfo = http( $userinfo_url , $params );
                $userinfo = json_decode($userinfo);
                $unionid = empty($userinfo->unionid) ? $userinfo->unionid : '';

                $data = array(
                 'wx_open_id' => $result->openid
                ,'wx_union_id' => $userinfo->unionid
                ,'buyer_nick' => $userinfo->nickname
                ,'sex' => $userinfo->sex
                ,'province' => $userinfo->province
                ,'city' => $userinfo->city
                ,'buyer_img' => $userinfo->headimgurl
                );
                $sql = "select buyer_id from ax_users where wx_open_id = '$result->openid'";
                $results = M()->query($sql);
                if(!empty($data['wx_open_id'])){
                 $arr['nickname'] = $userinfo->nickname;
                 $arr['openid'] = $result->openid;
                 $arr['buyer_img'] = $userinfo->headimgurl;
                 session('userInfo',$arr,7200*24);
                    if(!empty($data['wx_open_id']) && empty($results)){
                        M('Users')->add($data);
                    }else if(!empty($data['wx_open_id']) && !empty($results)){
                        $data['buyer_id'] = $results[0]['buyer_id'];
                        M('Users')->save($data);
                    }
                }

                header('Location: ' . 'http://' . $_SERVER['HTTP_HOST'] );
            }
        }
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

    public function creationQrcode (){
        $token = session("access_token");
        $url   = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
        $param['action_name '] = "QR_LIMIT_SCENE";
        $param['action_info ']['scene']['scene_id'] = 123;
        $param = json_encode($param);
    }

    /**
     * 我的空间主页
     */
    public function indexList(){
        fb($this->userInfo['openid']);
       // $sql = "select * from ax_users where wx_open_id = \'. $this->userInfo['openid'] .\'";
        $this->display('indexList');
    }


}