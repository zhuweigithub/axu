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


	/*public function generateTick($flush = false)
	{
		$wxAccessToken = $this->wxCallbackApi->generateToken($flush);
		self::$sesSuffixJs =$this->appId . WeixinConst::PRE_WX_ACCESS_TOKEN_JSAPI;
		$jsApiTick = $this->redis->get(self::$sesSuffixJs);
		if ($flush || empty($jsApiTick)){
			$urlJsAccessToken = config('common.weiXinBaseUrl') . "/cgi-bin/ticket/getticket?access_token={$wxAccessToken}&type=jsapi";
			//$info = CurlClient::getInstance()->doGet($urlJsAccessToken);
			$info = $this->api->get($urlJsAccessToken);
			$arr = json_decode($info, true);
			if (!empty($arr) && is_array($arr)){
				$jsApiTick = $arr['ticket'];
				if (!empty($jsApiTick)){
					$accessExpires = 60 * 60 * 2 - 100;
					if ($arr["expires_in"] > 0){
						$accessExpires = $arr["expires_in"] - 100;
					}
					$rs = $this->redis->setex(self::$sesSuffixJs, $accessExpires, $jsApiTick);
				}
				else {
					//LoggerFactory::getLogger(__CLASS__)->error("weixin get js tick error 1:", $arr);
					return false;
				}
			}
			else {
				//LoggerFactory::getLogger(__CLASS__)->error("weixin get js tick error 2:" . $info);
				return false;
			}
		}
		return $jsApiTick;
	}*/

    public function generateTick($flush = false) {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("jsapi_ticket.json"));
		print_r($data);exit;
        if ($data->expire_time < time()) {
            $accessToken = $this->wxCallbackApi->generateToken($flush);
            // 如果是企业号用以下 URL 获取 ticket
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

	//public function getSignPackage($url, $flush = false)
	public function getSignPackage( $flush = false)
	{
		$url = "http://admin.axu.com/index.php/Home/WxUploadImg/imgUpload";

		$jsApiTicket = $this->generateTick($flush);
		print_r($jsApiTicket);exit;
		$timestamp = time();
		$nonceStr = self::createNonceStr();
		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket={$jsApiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url=" . $url;
		$signature = sha1($string);
		$signPackage = array(
			"appId" =>$this->appId,
			"nonceStr" => $nonceStr,
			"timestamp" => $timestamp,
			"url" => $url,
			"signature" => $signature,
			"rawString" => $string
		);
       print_r($signPackage);
		return $signPackage;
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

}
