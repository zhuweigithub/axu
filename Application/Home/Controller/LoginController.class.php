<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function __construct()
    {
        parent::__construct();
    }
    public function login(){
        $data['username']='';//json 格式  参数需要组装   username     password  这里参数需要些验证什么的，
        $data['password']='';
        $verify = D("Login");
        $result = $verify -> login( $data );//登录的接口
        fb($result);
        $this->display();
    }
    public function loginVerify(){


        $this->display();
    }
}