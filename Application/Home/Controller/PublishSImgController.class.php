<?php
namespace Home\Controller;
use Think\Controller;
import('Lib.CommonClass.lib_image_imagick');
class PublishSImgController extends Controller{
    public function __construct(){
        parent::__construct();
    }
	
	/*
	 * 进入“发布私密照”页面的action
	 */
	public function publishSImg(){
		//如果session为空则退回到登陆界面
		$_SESSION['userId']=998;
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){
			$this->display('Tips:welcome');
		}else{		
			$this->display();
		}	
	}
	
	
	
	
	

		
}