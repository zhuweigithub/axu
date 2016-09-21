<?php
namespace Home\Controller;
use Think\Controller;
import('Lib.CommonClass.lib_image_imagick');
class MyPublishController extends FatherController{
    public function __construct(){
        parent::__construct();
    }
	
	/*
	 * 进入“我的发布”页面的action
	 */
	public function myPublish(){
		//如果session为空则退回到登陆界面
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){ 
			$this->display('Tips:welcome');
		}else{
            $this->getUser();
            $result = $this->userMessage;
            $niceName = $this->gStr($result[0]['buyer_nick']);//用户昵称，从数据库获取
            $jifen = $result[0]['integral'];//账户余额，从数据库获取
            $avatar = 'http://localhost/axu/Public/img/20151102181644_QMNxw.thumb.224_0.jpeg';//用户头像url地址
            $avatar =  empty( $result[0]['buyer_img'] ) ? $avatar : $result[0]['buyer_img'];
			$count=18;
			//下面的收入详情的数组有数据查询获得		
			$this->assign('niceName',$niceName);
			$this->assign('jifen',$jifen);
			$this->assign('avatar',$avatar);
			$this->assign('count',$count);
			$this->display();	
		}
	}
	
	
	/*
	 * ajax获取“我的发布”列表的接口
	 */
	public function publishedListRest(){
		
		$num=$_POST['num'];//获取每页记录数
		$page=$_POST['page'];//获取请求的页码		
		$arr = array(
			'0'=>array(
				"secretID"=>"1",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记1",
				"publishTime"=>"2016-07-05 22:10:56"	
			), 
			'1'=>array(
				"secretID"=>"2",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记2",
				"publishTime"=>"2016-07-05 22:10:56"	
			),
			'2'=>array(
				"secretID"=>"3",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记3",
				"publishTime"=>"2016-07-05 22:10:56"	
			),
			'3'=>array(
				"secretID"=>"4",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记4",
				"publishTime"=>"2016-07-05 22:10:56"	
			),
			'4'=>array(
				"secretID"=>"5",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记5",
				"publishTime"=>"2016-07-05 22:10:56"	
			),
			'5'=>array(
				"secretID"=>"6",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记6",
				"publishTime"=>"2016-07-05 22:10:56"	
			),
			'6'=>array(
				"secretID"=>"7",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记7",
				"publishTime"=>"2016-07-05 22:10:56"	
			)
	
		);
		
		$data['status'] = 0;
		$data['info'] = $arr;
		$this->ajaxReturn($data,'JSON');
		
	}
		
}