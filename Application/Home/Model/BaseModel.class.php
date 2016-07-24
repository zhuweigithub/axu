<?php
namespace Common\Model;
/**
 * auth zw
 * email 452436132@qq.com
 * Class BaseModel
 * @package Common\Model
 */
class BaseModel {
    public $_loginMessage = "";
    public function __construct(){

        $this->_loginMessage = session("loginMessage");

    }
    public $error_msg = array(
        "1001" => "参数不能为空",
        "1002" => "用户名或密码错误",
        "1003" => "注册失败，发生未知错误",
        "1004" => "没有更多数据了",
        "1005" => "活动添加失败，发生未知错误",
    );

    /**
     * 判断是否登录，没有登录跳登录页
     */
    public function loginInfo(){
        if( empty($this->_loginMessage )){
            header("Login/index");exit;
        }
    }

    /** 请求错误
     * @param $data
     * @return mixed
     */
    public function errJson($data){
        $param['status'] = "0";
        $param['result'] = $data;
        return json_decode($param);
    }

    /**数据请求成功返回状态和数据
     * @param $userInfo
     * @return mixed
     */
    public function successRequest($userInfo){
        $param['status'] = "1";
        $param['result'] = $userInfo;
        return json_decode($param);

    }

    /** 登录或者微信授权进入后设置session
     * @param $buyerId
     * @param string $mobile
     * @param $buyer_nick
     * @param string $wx_open_id
     */
    public function  saveSession($buyerId,$mobile = '',$buyer_nick,$wx_open_id = ''){
        $arr = array(
            "buyerId" => $buyerId
            ,"mobile" => $mobile
            ,"buyer_nick" => $buyer_nick
            ,"wx_open_id" => $wx_open_id
        );
        session(array(
            'loginMessage' => $arr
        ,'expire'=>3600
        ));
    }

    /**
     * 注销登录
     */
    public function logout(){
        session('loginMessage', null);
    }
}