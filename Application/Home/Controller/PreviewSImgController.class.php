<?php
namespace Home\Controller;
use Think\Controller;
import('Lib.CommonClass.lib_image_imagick');
class PreviewSImgController extends Controller{
    public function __construct(){
        parent::__construct();
    }
	
	/*
	 * 进入“收入详情”页面的action
	 */
    public function previewSImg(){
    	//如果session为空则退回到登陆界面
		if(!isset($_SESSION['userId'])||$_SESSION['userId']==''){
			$this->display('Tips:welcome');
		}else{
			
			$publishTitle = isset($_POST['publishTitle'])?$_POST['publishTitle']:'';
			$secretIntro = isset($_POST['secretIntro'])?$_POST['secretIntro']:'';			
			$minValue = isset($_POST['minValue'])?$_POST['minValue']:'';
			$maxValue = isset($_POST['maxValue'])?$_POST['maxValue']:'';
			
						
			$this->assign('publishTitle',$publishTitle);
			$this->assign('secretIntro',$secretIntro);
			$this->assign('minValue',$minValue);
			$this->assign('maxValue',$maxValue);
			
		    $dest_folder   =  "Public/upload/";
		 	if(!file_exists($dest_folder)){
		        mkdir($dest_folder);
		 	}
			
			$maxsize=2000000; //2M
			//step 2 使用$_FILES["pic"]["size"] 限制大小 单位字节 2M=2000000
			if($_FILES["img"]["size"] > $maxsize ) {
			  echo "<script type='text/javascript'>alert('上传的文件太大，不能超过{$maxsize}字节');history.back();</script>";
			  exit;
			}else{
				$allowtype=array("png", "gif", "jpg", "jpeg");
				
				foreach($_FILES["fileselect"]["error"] as $key => $error){
				    if ($error == UPLOAD_ERR_OK){
				    	$arr=explode(".",$_FILES["fileselect"]["name"]);
						$hz=$arr[count($arr)-1];
						if(!in_array($hz,$allowtype)){
							echo "<script type='text/javascript'>alert('这是不允许的类型');history.back();</script>";
						    exit;
						}
						$randname=date("Y").date("m").date("d").date("H").date("i").date("s").rand(100, 999).".".$hz;
				        //$tmp_name = $_FILES["fileselect"]["tmp_name"][$key];//按文件上传名保存
				        $name = $_FILES["fileselect"]["name"][$key];
				      	$uploadfile = $dest_folder.$name;
				        move_uploaded_file($tmp_name, $uploadfile);
				    }else{
				    	echo "<script type='text/javascript'>alert('上传失败');history.back();</script>";
					}
					
				}
				
				
				
				
				
				
				
				
				
				
				

				
				
			
				    
				      //将临时位置的文件移动到指定的目录上即可
				    if(is_uploaded_file($_FILES["img"]["tmp_name"])){
				        if(move_uploaded_file($_FILES["img"]["tmp_name"],$filepath.$randname)){
				          	echo "<script type='text/javascript'>history.back();</script>";
				         	session_start();
				          	$_SESSION['images'] = $fileimgweb.$randname;
				        }else{
				          	
				        }
				    }else{
				        echo"<script type='text/javascript'>alert('不是一个上传文件');history.back();</script>";
				    }   
				}
				
				
				
				
			}
			
			

			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			$this->assign('maxValue',$_FILES["fileselect"]["tmp_name"][0]);		
			$this->display();
		}
		
    }
}