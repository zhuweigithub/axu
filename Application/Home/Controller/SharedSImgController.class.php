<?php
namespace Home\Controller;
use Think\Controller;
import('Lib.CommonClass.lib_image_imagick');
class MyIncomeController extends Controller{
    public function __construct(){
        parent::__construct();
    }
	
	/*
	 * 进入“收入详情”页面的action
	 */
    public function myIncome(){
    	//如果session为空则退回到登陆界面 
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){
			$this->display('Tips:welcome');
		}else{
			$totalIncome=126.8;//wo的收入总计，从数据库获取
			$niceName='小猪猪';//用户昵称，从数据库获取
	        $jifen=182;//账户余额，从数据库获取
	        $avatar='http://localhost/axu/Public/img/20151102181644_QMNxw.thumb.224_0.jpeg';//用户头像url地址
			//下面的收入详情的数组有数据查询获得			
			$this->assign('totalIncome',$totalIncome);
			$this->assign('niceName',$niceName);
			$this->assign('jifen',$jifen);
			$this->assign('avatar',$avatar);
				
			$this->display();
		}
    }
	
}