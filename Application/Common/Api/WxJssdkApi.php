<?php

//use Dash\Curl\CurlClient;

//use Dash\Logger\LoggerFactory;
class WxJssdkApi extends BaseLibrary
{

    private static $sesSuffixJs;
    private $wxCallbackApi;

    public function __construct()
    {
        parent::__construct();
        $this->wxCallbackApi = $this->di->get('wxCallbackApi');

        $this->generateTick();
    }

    public function generateTick($flush = false)
    {
        $wxAccessToken = $this->wxCallbackApi->generateToken($flush);
        self::$sesSuffixJs = config('common.appId') . WeixinConst::PRE_WX_ACCESS_TOKEN_JSAPI;
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
    }

    public function getSignPackage($url, $flush = false)
    {
        
        $jsApiTicket = self::generateTick($flush);
        $timestamp = time();
        $nonceStr = self::createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket={$jsApiTicket}&noncestr={$nonceStr}&timestamp={$timestamp}&url=" . $url;
        $signature = sha1($string);
        $signPackage = array(
            "appId" => config('common.appId'),
            "nonceStr" => $nonceStr,
            "timestamp" => $timestamp,
            "url" => $url,
            "signature" => $signature,
            "rawString" => $string
        );

        $this->logger->debug('WXJSSDK==>JS_SIGN_PACKAGE:' . var_export($signPackage, true));
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
