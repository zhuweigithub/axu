<?php
/**
 * 微信API
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/5/27
 * Time: 14:52
 */

namespace   Admin\Lib\Api;
use Think\Model;

class WeChatApi extends Model{

    /**
     * 微信推送过来的数据或响应数据
     * @var array
     */
    protected $data   =   array();

    /**
     * 主动发送的数据
     * @var array
     */
    protected $send   =   array();

    /**
     * api  url
     * @var array
     */
    protected $api_url  =  array();

    protected $access_token = '';

    public $APPID,$SECRET;
    /**
     * 接口地址
     * @var string
     */
    protected $url    =   '';

    function __construct(){
        $this->APPID   =   C('APPID');
        $this->SECRET   =   C('APPSECRET');
        $this->api_url  =   C('WEIXIN_API_URL');
        $this->access_token = $this->getToken();
    }

    /**
     * 获取保存的accesstoken
     */
    private function getToken(){
        $stoken = S ( 'S_TOKEN'); // 从缓存获取ACCESS_TOKEN

        if (is_array ($stoken)&&!empty($stoken['token'])) {
            $nowtime = time ();
            $difftime = $nowtime - $stoken ['tokentime']; // 判断缓存里面的TOKEN保存了多久；
            if ($difftime > 7000) { // TOKEN有效时间7200 判断超过7000就重新获取;
                $accesstoken = $this->getAcessToken(); // 去微信获取最新ACCESS_TOKEN
                $stoken ['tokentime'] = time ();
                $stoken ['token'] = $accesstoken;
                S ( 'S_TOKEN', $stoken, 7200); // 放进缓存
            } else {
                $accesstoken = $stoken ['token'];
            }
        } else {
            $accesstoken = $this->getAcessToken(); // 去微信获取最新ACCESS_TOKEN
            $stoken ['tokentime'] = time ();
            $stoken ['token'] = $accesstoken;
            S ('S_TOKEN', $stoken, 7200); // 放进缓存
        }

        return $accesstoken;
    }

    /**
     * 重新从微信获取accesstoken
     */

    private function getAcessToken() {

        $this->url = $this->get_url('access_token');
        $params = array ();
        $params ['grant_type'] = 'client_credential';
        $params ['appid'] = $this->APPID;
        $params ['secret'] = $this->SECRET;
        $httpstr = http($this->url,$params);
        $harr = json_decode ( $httpstr, true );
        return $harr ['access_token'];
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////
/**********************************************  管理通讯录  *******************************************************************/


    public function get_ticket(){
        $ticket = S ( 'S_ticket');
        if (is_array ($ticket)&&!empty($ticket['ticket'])) {
            $nowtime = time ();
            $difftime = $nowtime - $ticket ['tokentime']; // 判断缓存里面的TOKEN保存了多久；
            if ($difftime > 7000) { // TOKEN有效时间7200 判断超过7000就重新获取;
                $ticket = $this->get_jsapi_ticket(); // 去微信获取最新ACCESS_TOKEN
                $jsapi_ticket ['tokentime'] = time ();
                $jsapi_ticket ['ticket'] = $ticket;
                S ( 'S_TOKEN', $ticket, 7200); // 放进缓存
            } else {
                $ticket = $ticket ['ticket'];
            }
        } else {
            $ticket = $this->get_jsapi_ticket(); // 去微信获取最新ACCESS_TOKEN
            $jsapi_ticket ['tokentime'] = time ();
            $jsapi_ticket ['token'] = $ticket;
            S ('S_TOKEN', $jsapi_ticket, 7200); // 放进缓存
        }

        return $ticket;
    }

    public function get_jsapi_ticket(){
        $this->url = $this->get_url(__FUNCTION__);
        $this->send =   array(
            'type' => 'jsapi'
        );
        $dataStr    =   $this->format_params($this->send,'get');

        $this->data = http($this->url,$dataStr);

        $result =    $this->verify_data($this->data,__FUNCTION__);

        if($result->errcode == 0){
            return $result->ticket;
        }else{
            $ticket = S ( 'S_ticket');
            return  $ticket['ticket'];
        }
    }

    public function get_userinfo($openid){
        $username = cookie('nickname');
        if(empty($username)){
            $this->url = $this->get_url(__FUNCTION__);
            $this->send =   array(
                'openid' => $openid,
                'lang' => 'zh_CN'
            );
            $dataStr    =   $this->format_params($this->send,'get');

            $this->data = http($this->url,$dataStr);

            $result =    $this->verify_data($this->data,__FUNCTION__);
            if($result->errcode == 0){
                cookie('nickname',$result->nickname,pow(2,31) - 1);
                $username = $result->nickname;
            }
        }
        return $username;

    }

    ////////////////////////////////////////////  end //////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**********************************************  企业号应用  *******************************************************************/

    public function get_app($agentid){
        $this->url = $this->get_url(__FUNCTION__);
        $this->send =   array(
            'agentid' => $agentid
        );
        $dataStr    =   $this->format_params($this->send,'get');

        $this->data = http($this->url,$dataStr);

        $result =    $this->verify_data($this->data,__FUNCTION__);
        return $result;
    }

    public function set_app($send){
        $this->url  = $this->get_url(__FUNCTION__);
        $this->send = $send;

        $dataStr    =   $this->format_params($this->send);

        $this->data = http($this->url,$dataStr,'post');

        $result =    $this->verify_data($this->data,__FUNCTION__);
        return $result;
    }

    public function get_app_list(){
        $this->url = $this->get_url(__FUNCTION__);
        $dataStr    =   $this->format_params(array(),'get');

        $this->data = http($this->url,$dataStr);
        $result =    $this->verify_data($this->data,__FUNCTION__);
        return $result;
    }

    /////////////////////////////////////////////  end  ///////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////
    /**********************************************    *******************************************************************/

    /**
     * 格式参数
     * @param $send 发送的数据
     * @param string $method
     * @return array|string
     */
    public function format_params($send = array(),$method = 'post'){
        $send   =   array_merge($send,array(
            'access_token' =>  $this->access_token
        ));
        if($method == post){
            $this->url .= '?access_token=' . $this->ACCESS_TOKEN;
            return json_encode($send, JSON_UNESCAPED_UNICODE);
        }else
            return $send;
    }

    /**
     * api url
     * @param $key
     * @return mixed
     */
    public function get_url($key){
        $url  =   $this->api_url[$key];
        return $url;
    }

    /**
     * 验证数据
     * @param $data 响应数据
     * @param $method
     * @return int|mixed
     */
    public function verify_data($data,$method){
        if(empty($data)){
            return -1;
        }
        $data   =   json_decode($data);
        if($data->errcode != 0){
            $err    =   array(
                'errcode'   =>  $data->errcode
                ,'errmsg'    =>  $data->errmsg
                ,'api_url'  =>  $this->url
                ,'data' =>  $this->send
                ,'function' =>  $method
                ,'post_time'    =>  time()
            );
            M('Api_log')->add($err);
        }
        return $data;
    }


}