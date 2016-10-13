<?php
namespace Home\Controller;
use Think\Controller;
class WxUploadImgController extends FatherController{
	public function __construct(){
		parent::__construct();
	}
	public function imgUpload(){

		$this->display();
	}
	public function getSignPackage(){
		$url = $_POST['url'];
		$url = urldecode($url);

		$signPackage = $this->wxJssdkApi->getSignPackage($url);

		$signPackage['back_url'] = $this->request->get("back_url");

		echo json_encode($signPackage);
	}


}
