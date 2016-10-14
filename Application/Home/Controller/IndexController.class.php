<?php
namespace Home\Controller;

use Common\Api\WxApi;
use Think\Controller;

load('Common.WxApi');
class IndexController extends FatherController
{
	protected $_appId = "";
	protected $_appSecret = "";
	protected $_wxApi = "";
	const GET_CODE_URL = "http://www.zhuwei.site/index.php/Home/Index/getList";
	public function __construct()
	{
		$this->_appId     = C("APP_ID");
		$this->_appSecret = C("APP_SECRET");
		$this->_wxApi     = new WxCallbackController();

		parent::__construct();

	}

	public function index()
	{
		if (!$this->getUserInfo()) {
			$redirect_url = self::GET_CODE_URL;
			$this->_wxApi->getWxCode($redirect_url);
		} else {
			$this->indexList();
		}
	}

	public function getList()
	{
		if (!$_GET['code']) {
			throw new Exception("code获取异常");
		}
		$result = $this->_wxApi->getAccessTokenByCode($_GET['code']);
		if (!empty($result)) {
			$userInfo = $this->_wxApi->getUserInfoByAccessToken($result);
			$openId   = $userInfo['wx_open_id'];
			$sql      = "select buyer_id from ax_users where wx_open_id = '$openId'";
			$results  = M()->query($sql);
			if (!empty($userInfo['wx_open_id'])) {
				$arr['nickname']  = $userInfo['buyer_nick'];
				$arr['openid']    = $userInfo['wx_open_id'];
				$arr['buyer_img'] = $userInfo['buyer_img'];
				session('userInfo', $arr, 7200 * 24);
				if (!empty($userInfo['wx_open_id']) && empty($results)) {
					M('Users')->add($userInfo);
				} else if (!empty($userInfo['wx_open_id']) && !empty($results)) {
					$userInfo['buyer_id'] = $results[0]['buyer_id'];
					M('Users')->save($userInfo);
				}
			}
			header('Location: ' . 'http://' . $_SERVER['HTTP_HOST']);
		}
	}

	public function checkSignature()
	{
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce     = $_GET["nonce"];

		$token  = "zhuwei";
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);

		if ($tmpStr == $signature) {
			$echoStr = $_GET["echostr"];
			echo $echoStr;
			return true;
		} else {
			return false;
		}
	}

	public function creationQrcode()
	{
		$token                                      = session("access_token");
		$url                                        = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
		$param['action_name ']                      = "QR_LIMIT_SCENE";
		$param['action_info ']['scene']['scene_id'] = 123;
		$param                                      = json_encode($param);
	}

	/**
	 * 我的空间主页
	 */
	public function indexList()
	{
		fb($this->userInfo['openid']);
		// $sql = "select * from ax_users where wx_open_id = \'. $this->userInfo['openid'] .\'";
		$this->display('indexList');
	}


}