<?php
namespace Home\Controller;
use Think\Controller;
import('Lib.CommonClass.lib_image_imagick');
class TipsController extends Controller{
    public function __construct(){
        parent::__construct();
    }
	
	/*
	 * 进入“收入详情”页面的action
	 */
    public function welcome(){
    	//如果session为空则退回到登陆界面
		
		$this->display();
    }

	
	
		
}