<?php
namespace Home\Controller;
use Think\Controller;
import('Lib.CommonClass.lib_image_imagick');
class ZoomController extends FatherController{
    public function __construct(){
        parent::__construct();
    }
	
	public function myZoom(){
		//如果session为空则退回到登陆界面
		$_SESSION['userId']=998;
		
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){
			$this->display('Tips:welcome');
		}else{
            $this->getUser();
            $result = $this->userMessage;
			$balance = round($result[0]['balance']/100,2);//账户余额，从数据库获取
			$niceName = $this->gStr($result[0]['buyer_nick']);//用户昵称，从数据库获取
	        $jifen = $result[0]['integral'];//账户余额，从数据库获取
	        $avatar = 'http://localhost/axu/Public/img/20151102181644_QMNxw.thumb.224_0.jpeg';//用户头像url地址
            $avatar =  empty( $result[0]['buyer_img'] ) ? $avatar : $result[0]['buyer_img'];
			$this->assign('userId',$_SESSION['userId']);
			$this->assign('balance',$balance);
			$this->assign('niceName',$niceName);
			$this->assign('jifen',$jifen);
			$this->assign('avatar',$avatar);   
			$this->display();
					
		}	
		
	}

	
	/*
	 * 进入“收入详情”页面的action
	 */
    public function myIncomeList(){
    	//如果session为空则退回到登陆界面
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){
			$this->redirect('Login/index');
		}
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
	
	
	/*
	 * 查看wo发布过的私密图片的action
	 */
	public function mySecretImgDetail(){
		$secretId=$_GET['secretId'];//获取get请求过来的'secretId'
		
		//根据secretId获取我发布的私密图的详情信息
		$niceName='小猪猪';//登录用户的昵称，从数据库获取
        $publishTime='2016-07-16 20:15:30';//wo的收入总计，从数据库获取
        $avatar='http://localhost/axu/Public/img/20151102181644_QMNxw.thumb.224_0.jpeg';//用户头像url地址
		$secretTitle='我的日记，你想看吗？';//私密图的标题
		$secretImgUrl='http://localhost/axu/Public/img/riji.jpg';//私密图的url地址
		$secretIntro='这是我的日记，我的个人经历，和大伙觉得很骄傲哈。记得哈活动卡活动和骄傲的哈酒活动空间哈就好的骄傲，哈记得哈就好打客户的骄傲和简单很骄傲和大家啊！按电话卡或接口等哈就喝酒打架的安徽的卡号的号角！';
		$minValue=2;
		$maxValue=6;
		//下面的收入详情的数组有数据查询获得			
		$this->assign('niceName',$niceName);
		$this->assign('publishTime',$publishTime);
		$this->assign('avatar',$avatar);
		$this->assign('secretTitle',$secretTitle);
		$this->assign('secretImgUrl',$secretImgUrl);
		$this->assign('secretIntro',$secretIntro);
		$this->assign('minValue',$minValue);
		$this->assign('maxValue',$maxValue);
		$this->display();
	}
	
	
	/*
	 * 发布私密图的action
	 */
	public function publishSecretImg(){
		//如果session为空则退回到登陆界面
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){
			$this->redirect('Login/index');
		}
		

		$this->display();			
	}
	
	/*
	 * 进入分享后的私密图页面的action
	 */
	public function mySharedSecretImg(){
		$secretTitle=$_POST['secretTitle'];
		$secretIntro=$_POST['secretIntro'];
		$minValue=$_POST['minValue'];
		$maxValue=$_POST['maxValue'];
		
		$imgUrlList = array();	
		for($i=0;$i<count($_FILES['fileselect']['name']);$i++){
		  	$tmpFilePath = $_FILES['fileselect']['tmp_name'][$i];
		  	if($tmpFilePath!=''){
			    $newFilePath = "Public/upload/". $_FILES['fileselect']['name'][$i];
			    if(move_uploaded_file($tmpFilePath, $newFilePath)){
			    	$imgUrlList[$i]='http://localhost/axu/Public/upload/'.$_FILES['fileselect']['name'][$i];
				}				
		  	}
		}

		//此处若上传成功，则要生成一个secretId，同时要输出一张高斯模糊后的图片
							
		//根据secretId获取我发布的私密图的详情信息
		if(count($imgUrlList)>0){
			$niceName='小猪猪';//登录用户的昵称，从数据库获取
	        $publishTime='2016-07-16 20:15:30';//wo的收入总计，从数据库获取
	        $avatar='http://localhost/axu/Public/img/20151102181644_QMNxw.thumb.224_0.jpeg';//用户头像url地址
			
			//下面的收入详情的数组有数据查询获得			
			$this->assign('niceName',$niceName);
			$this->assign('publishTime',$publishTime);
			$this->assign('avatar',$avatar);
			
			
			$this->assign('secretTitle',$secretTitle);
			$this->assign('imgUrlList',$imgUrlList);
			$this->assign('secretIntro',$secretIntro);
			$this->assign('minValue',$minValue);
			$this->assign('maxValue',$maxValue);
			
			$this->display();

	    }else{
	    	echo "发布失败，请重新发布！";
	    }
								
	}


	


	

	


	

	/*
	 * 查看“好友的私密”的action
	 */
	public function otherSharedSecretImg(){
		$secretId=$_GET['secretId'];
		//此处需要根据secretId查询数据库，确定session中登录的该用户是否已经付款查看过该私密照，如果以前成功查看过，则返回清晰图片的URL数组，如果没有则返回高斯模糊处理后的image对象的数组。
		
		/*测试数据*/
		$imgUrlList = array();
		$imgUrlList[0]='http://localhost/axu/Public/img/riji.jpg';
		$imgUrlList[1]='http://localhost/axu/Public/img/riji.jpg';
				
		$otherNiceName='小猪猪';//登录用户的昵称，从数据库获取
        $publishTime='2016-07-16 20:15:30';//wo的收入总计，从数据库获取
        $avatar='http://localhost/axu/Public/img/20151102181644_QMNxw.thumb.224_0.jpeg';//用户头像url地址
        $secretTitle='一见难忘的事，你想知道吗？';
		$secretIntro='这是一件难忘的往事，我的轻身经历，哈哈哈哈哈哈，快来围观！！';
		//下面的收入详情的数组有数据查询获得			
		$this->assign('otherNiceName',$otherNiceName);
		$this->assign('publishTime',$publishTime);
		$this->assign('avatar',$avatar);
		$this->assign('secretTitle',$secretTitle);
		$this->assign('imgUrlList',$imgUrlList);
		$this->assign('secretIntro',$secretIntro);
	
		$this->display();
	}
	
	/*
	 * 用户签到接口
	 */
	public function signRest(){
		//如果session为空则退回到登陆界面
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){
			//$this->redirect('Login/index');
			$data['retn'] = 0;
			$data['desc'] = '未登录';
		}else{
			//此处做积分数据处理，如果签到后数据库积分更新正常则返回“1”，否则返回“2”
			$data['retn'] = 1;
			$data['desc'] = '签到成功';
		}
		
		$this->ajaxReturn($data,'JSON');
			
	}
	
		
}