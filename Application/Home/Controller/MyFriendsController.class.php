<?php
namespace Home\Controller;
use Think\Controller;
import('Lib.CommonClass.lib_image_imagick');
class MyFriendsController extends FatherController{
    public function __construct(){
        parent::__construct();
    }
	
	/*
	 * 进入“我的人脉”页面的action
	 */
	public function myFriends(){
		//如果session为空则退回到登陆界面
		$_SESSION['userId']=998;
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){
			$this->display('Tips:welcome');
		}else{
			$friendsCount=12;//wo的收入总计，从数据库获取
            $this->getUser();
            $result = $this->userMessage;
            $niceName = $this->gStr($result[0]['buyer_nick']);//用户昵称，从数据库获取
            $jifen = $result[0]['integral'];//账户余额，从数据库获取
            $avatar = 'http://localhost/axu/Public/img/20151102181644_QMNxw.thumb.224_0.jpeg';//用户头像url地址
            $avatar =  empty( $result[0]['buyer_img'] ) ? $avatar : $result[0]['buyer_img'];
			$this->assign('friendsCount',$friendsCount);
			$this->assign('niceName',$niceName);
			$this->assign('jifen',$jifen);
			$this->assign('avatar',$avatar);
				
			$this->display();	
		}	
	}

		
}