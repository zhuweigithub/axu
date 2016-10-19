<?php
namespace Home\Controller;
use Think\Controller;
use Home\Lib\Util\CHTTPExceptions;
use Home\Lib\Util\Response;


class FatherController extends Controller {

    protected $userInfo = '';
    protected $userMessage = '';
    protected $open_id;
    protected $_appId;
    protected $_appSecret;
	protected $_response;
    public function __construct()
    {
        parent::__construct();
		$this->open_id = session("userInfo")['wx_open_id'];
		$this->_appId     = C("APP_ID");
		$this->_appSecret = C("APP_SECRET");
		$this->_response  = new Response();

		set_exception_handler(function (CHTTPExceptions $exception) {
			$exception->send([$this->_response, 'send']);
		});
    }



    /** 检查是否有授权过
     * @return bool
     */
    public function getUserInfo(){
		$this->userInfo = session("userInfo");
        if(empty($this->userInfo)){
            return false;
        }else{
            return true;
        }
    }


	/** 获取用户信息
	 */
	public function getUser(){
		$buyer_id = session("userInfo")['wx_open_id'];
        $db = M();
        $sql = "select * from ax_users as a ,ax_user_ext as b where a.buyer_id = b.buyer_id and a.status = 0 and a.buyer_id = " .$buyer_id;
        $result = $db->query($sql);
        $this->userMessage = $result;
    }


	/** 获取用户id
	 */
	public function getBuyerId(){
		//$wxOpenId = $this->open_id;
		$wxOpenId = "ovrgAv3nbdTq4r_tHZePpz3tLvlw";
		$result = M("Users")->field("buyer_id")->where("wx_open_id = '$wxOpenId ' and status = 0")->find();
		return $result['buyer_id'];
	}
    protected  function gStr($str)
    {
        $encode = mb_detect_encoding( $str, array('ASCII','UTF-8','GB2312','GBK'));
        if ( !$encode =='UTF-8' ){
            $str = iconv('UTF-8',$encode,$str);
        }
        return $str;
    }

	/**
	 * 未登陆跳转到登陆界面
	 */
	public function login() {
		if ($this->isLogin()) {
			return ;
		}
		$redirectURL = $this->buildMenuUrl("/login?back=".$this->request->get("_url"));
		if ($this->isAjax()) {
			$this->jsonOut(-10000, "您尚未登录", ['back' => $redirectURL]);
			exit;
		}

		header("Location:" . $redirectURL);
		exit;
	}

	public function isLogin() {
		if (!$this->open_id) {
			return false;
		}
		return true;
	}

	protected function buildMenuUrl($uri) {
		$oauthUrl = "https://open.weixin.qq.com/connect/oauth2/authorize";
		$baseUrl = "http://www.zhuwei.site/";
		$url = $oauthUrl . "?appid=" . $this->_appId . '&redirect_uri=' . urlencode($baseUrl . $uri) . '&response_type=code&scope=snsapi_base&state=266#wechat_redirect';
		return $url;
	}

    public function getRequestParam($val)
    {
        $data = $_POST[$val];
        if ($data) {
            return $data;
        }
        $data = $_GET[$val];
        if ($data) {
            return $data;
        }
        return '';
    }



}