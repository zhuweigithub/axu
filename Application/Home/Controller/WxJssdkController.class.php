<?php
namespace Home\Controller;
use Think\Controller;
class WxJssdkController extends FatherController{

	private static $sesSuffixJs;
	private $wxCallbackApi;
    private $appId;
    private $appSecret;

	public function __construct(){
		parent::__construct();
        $this->appId = C("APP_ID");
        $this->appSecret = C("APP_SECRET");
        $this->wxCallbackApi = new WxCallbackController();
		$this->generateTick();
	}
    public function generateTick($flush = false) {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例

        $data = json_decode(file_get_contents("jsapi_ticket.json"));
        if ($data->expire_time < time()) {
            $accessToken = $this->wxCallbackApi->generateToken($flush);
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";

            $res = json_decode($this->httpGet($url));
            $ticket = $res->ticket;
            if ($ticket) {
                $data->expire_time = time() + 7000;
                $data->jsapi_ticket = $ticket;
                $fp = fopen("jsapi_ticket.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {

            $ticket = $data->jsapi_ticket;
        }
        return $ticket;
    }

    private function httpGet($url) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;
    }

	public function getSignPackage($url, $flush = false)
	{
		//$url = "http://admin.axu.com/index.php/Home/WxUploadImg/imgUpload";
		$url = $_POST['url'];
		$url = urldecode($url);
		$jsApiTicket = $this->generateTick($flush);
		$timestamp = time();
		$nonceStr = $this->createNonceStr();
		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket={$jsApiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url=" . $url;
		$signature = sha1($string);
		$signPackage = array(
			"appId" =>$this->appId,
			"nonceStr" => $nonceStr,
			"timestamp" => $timestamp,
			"url" => $url,
			"signature" => $signature,
			"rawString" => $string,
			"back_url" => $_POST['back_url']
		);
		echo json_encode($signPackage);
	}


	private static function createNonceStr($length = 16)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++)
		{
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	public function downLoadPic()
	{
		$serverId = $_POST['serverId'];
		if(!$serverId){
			throw new Exception('serverID不能为空！');
		}
		$return   = array();
		$path     = 'Public/img/upload'; //定义保存路径
		$dir      = realpath($path); //为方便管理图片 保存图片时 已时间作一层目录作区分
		$tardir   = $dir . '/' . date('Y_m_d');
		if (!file_exists($tardir)) {
			mkdir($dir . '/' . date('Y_m_d'));
		}
		$wxCallback = new WxCallbackController();
		$accessToken = $wxCallback->generateToken();
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$accessToken."&media_id=".$serverId;
		try{
			$ch          = curl_init($url);
			$ranfilename = time() . rand() . ".jpg";
			$filename    = $path . '/' . date('Y_m_d') . '/' . $ranfilename; //存数据库用
			$tarfilename = $tardir . "/" . $ranfilename;
			$fp = fopen($tarfilename, "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			curl_close($ch);
			fclose($fp);
			echo $filename;
		}catch (Exception $e){
			throw new Exception($e);
		}

	}

}
