<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller{
    public function __construct(){
        parent::__construct();
    }
	
	public function index(){
		$this->display();
	}
	
    public function login(){
        $data['username']=$_POST['username'];//json 格式  参数需要组装   username     password  这里参数需要些验证什么的，
        $data['password']=$_POST['password'];
        /*$verify = D("Login");
        $result = $verify -> login( $data );//登录的接口
        fb($result);*/
		if($data['username']=='18061739088'&&$data['password']=='123456'){
			$_SESSION['username']='18061739088';
			$this->display('Zoom:myzoom');
		}else{
			//echo "登录失败！";
			$this->assign('result','用户名或密码不正确！');
			$this->display('index');
		}   
    }
	
		
	
	
    public function loginVerify(){
        $this->display();
    }
}