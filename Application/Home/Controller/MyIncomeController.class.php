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

	
	/*
	 * ajax获取“收入详情”列表的接口
	 */
	public function incomeListRest(){
		
		$num=$_POST['num'];//获取每页记录数
		$page=$_POST['page'];//获取请求的页码
		
		$arr = array(
			'0'=>array(
				"secretID"=>"1",
				"visiter"=>"风筝孩子王1",
				"visiterWX"=>"wx468955988",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记，你想看吗？",
				"secretValue"=>4.6,
				"visitTime"=>"2016-07-05 22:10:56"	
			), 
			'1'=>array(
				"secretID"=>"2",
				"visiter"=>"风筝孩子王2",
				"visiterWX"=>"wx468955988",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记，你想看吗？",
				"secretValue"=>6.6,
				"visitTime"=>"2016-07-05 22:10:56"	
			), 
			'2'=>array(
				"secretID"=>"3",
				"visiter"=>"风筝孩子王3",
				"visiterWX"=>"wx468955988",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记，你想看吗？",
				"secretValue"=>12.6,
				"visitTime"=>"2016-07-05 22:10:56"	
			), 
			'3'=>array(
				"secretID"=>"4",
				"visiter"=>"风筝孩子王4",
				"visiterWX"=>"wx468955988",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记，你想看吗？",
				"secretValue"=>8.6,
				"visitTime"=>"2016-07-05 22:10:56"	
			), 
			'4'=>array(
				"secretID"=>"5",
				"visiter"=>"风筝孩子王5",
				"visiterWX"=>"wx468955988",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记，你想看吗？",
				"secretValue"=>1.6,
				"visitTime"=>"2016-07-05 22:10:56"	
			), 
			'5'=>array(
				"secretID"=>"6",
				"visiter"=>"风筝孩子王6",
				"visiterWX"=>"wx468955988",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记，你想看吗？",
				"secretValue"=>3.9,
				"visitTime"=>"2016-07-05 22:10:56"	
			), 
			'6'=>array(
				"secretID"=>"7",
				"visiter"=>"风筝孩子王7",
				"visiterWX"=>"wx468955988",
				"secretImgUrl"=>"http://localhost/axu/Public/img/riji.jpg",
				"secretTitle"=>"我的日记，你想看吗？",
				"secretValue"=>2.8,
				"visitTime"=>"2016-07-05 22:10:56"
			)
			
		);
		
		$data['status'] = 0;
		$data['info'] = $arr;
		$this->ajaxReturn($data,'JSON');
		
	}

		
}