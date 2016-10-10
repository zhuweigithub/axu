<?php
namespace Home\Controller;
use Think\Controller;
use Think\Exception;

import('Lib.CommonClass.lib_image_imagick');
class PreviewSImgController extends Controller{
    public function __construct(){
        parent::__construct();
    }
	
	/*
	 * 进入“收入详情”页面的action
	 */
    public function previewSImg(){
    	header("content-type:text/html; charset=utf-8"); 
    	//如果session为空则退回到登陆界面
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){
			$this->display('Tips:welcome');
		}else{
			$imgArr=array();
			
			$publishTitle = isset($_POST['publishTitle'])?$_POST['publishTitle']:'';
			$secretIntro = isset($_POST['secretIntro'])?$_POST['secretIntro']:'';		
			$minValue = isset($_POST['minValue'])?$_POST['minValue']:'';    
			$maxValue = isset($_POST['maxValue'])?$_POST['maxValue']:'';

		    $dest_folder = "Public/upload/";
		 	if(!file_exists($dest_folder)){
		        mkdir($dest_folder);
		 	}
					dump($_FILES);
			foreach($_FILES["fileselect"]["name"] as $key=>$val){
			    	$arr=explode(".",$_FILES["fileselect"]["name"][$key]);
					$hz=$arr[count($arr)-1];
					$randname=date("Y").date("m").date("d").date("H").date("i").date("s").rand(100, 999).".".$hz;//按时间戳保存
			        $tmp_name = $_FILES["fileselect"]["tmp_name"][$key];
			        $name = $_FILES["fileselect"]["name"][$key];//按文件上传名保存
			      	$uploadfile = $dest_folder.$randname;
                    try{
                        move_uploaded_file($tmp_name, $uploadfile);
                    }catch (Exception $e){
                        throw $e;
                    }
					$imgArr[$key]='/Public/upload/'.$randname;

			}
         // print_r($imgArr);

		}

		//下面是造的假数据
		$avatar='/axu/Public/img/3.jpg';
		$niceName='小猪猪';
		$publishTime='2016-08-25';

		$this->assign('avatar',$avatar);//发布人头像，数据库获得
		$this->assign('niceName',$niceName);//发布人昵称，数据库获得
		$this->assign('publishTime',$publishTime);//发布时间，数据库获得
		
		$this->assign('publishTitle',$publishTitle);//发布的私密照“标题”
		$this->assign('secretIntro',$secretIntro);//发布的私密照“说说”
		$this->assign('minValue',$minValue);//发布的私密照“最小价值”
		$this->assign('maxValue',$maxValue);//发布的私密照“最大价值”
	
		$this->assign('imgArr',$imgArr);//发布的私密照链接地址，发布人自己发布预览的是原图	
		$this->display();
	}
}