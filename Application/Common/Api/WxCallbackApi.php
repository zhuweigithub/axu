<?php

//use Dash\Curl\CurlClient;
//use Dash\Logger\LoggerFactory;
/**
 * 视图中需要用到的方法
 * Class ViewHelper
 * @package
 */
class WxCallbackApi extends BaseLibrary {

    public $msgType = 'location';
    public static $sesSuffix;

    const WX_TOKEN = "/cgi-bin/token";
    const WX_ACCESS_TOKEN = "/sns/oauth2/access_token";
    const WX_REFRESH_TOKEN = "/sns/oauth2/refresh_token";
    const WX_USER_INFO = "/cgi-bin/user/info";
    const WX_USER_INFO_KEY_FMT = 'wx:user_info:%s';
    const MSG_START = "欢迎您使用在线咨询功能，请直接描述您的问题发送到公众号，我们正在联系药师为您解答（暂时只支持文字咨询）请稍等。";
    const MSG_IN_SESSION = "您正在使用在线咨询，请先回复Q退出咨询!";
    const MSG_END = "本次咨询已结束，欢迎您再次使用在线咨询。";

    public function __construct() {
        parent::__construct();
    }

    /**
     * 刷新微信TOKEN 
     * @param boolean $flush 是否强制刷新
     * @return string token
     */
    public function generateToken($flush = false) {
        $appId = config('common.appId');
        $appSecret = config('common.appSecret');
        $urlAccessToken = config('common.weiXinBaseUrl') . "/cgi-bin/token?grant_type=client_credential&appid={$appId}&secret={$appSecret}";

        //$keyToken = $appId . WeixinConst::PRE_WX_ACCESS_TOKEN;
        $keyToken = 'yib:site:token:' . SITE_ID;
        $token = $this->redis->get($keyToken);


        if ($token && !$flush) {
            return $token;
        }

        //没有获取到,或者要求强制刷新
        $token = $this->curl($urlAccessToken);


        if (!$token) {
            $this->logger->debug("ERROR:获取微信TOKEN失败!");
            return '';
        }

        //接口返回: {"access_token":"Nu9jllyxOd7YOlhJsRVxiXuaP-fka927pXBspAtfAsgSkySBmd5SAas1ADy7xcrPmQuvcEnZJSyOm2y4fH_NdDG4hLYQVlehzm-1oWrryEA","expires_in":7200}
        $token = json_decode($token, true);

        $this->redis->setex($keyToken, 1800, $token['access_token']);
        return $token['access_token'];
    }

    public function curl($url, $data = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPHEADER, []);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_COOKIEJAR, null);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $content = curl_exec($ch);

        return $content;
    }

    /**
     * 获取微信用户凭证
     * @param $code
     * @param bool $flush
     * @return array|bool|mixed
     */
    public function getUserInfoForWeblink($code, $fresh = false) {
        $appId = config('common.appId');
        ;
        $appSecret = config('common.appSecret');
        $rspUserUrl = config('common.weiXinBaseUrl') . self::WX_ACCESS_TOKEN . "?appid={$appId}&secret={$appSecret}&code={$code}&grant_type=authorization_code";
        //$strData = CurlClient::getInstance()->doGet($rspUserUrl);
        $strData = $this->api->get($rspUserUrl);
        $rspUser = json_decode($strData, true);
        if (isset($rspUser['errcode'])) {
            return false;
        }
        $userOpenId = $rspUser['openid'];

        if (!$fresh) {
            return $userOpenId;
        }
        $reflushUrl = config('common.weiXinBaseUrl') . self::WX_REFRESH_TOKEN . "?appid=" . config('common.appId') . "&grant_type=refresh_token&refresh_token=" . $rspUser['refresh_token'];
//        $strReflesh = CurlClient::getInstance()->doGet($reflushUrl);
        $strReflesh = $this->api->get($reflushUrl);
        $rspUserRefresh = json_decode($strReflesh, true);
        if ($rspUserRefresh['access_token']) {
            return $this->getUserInfo($userOpenId, $rspUserRefresh['access_token']);
        }
        return [];
    }

    /**
     * 消息类型
     */
    public function msgtype($post_info) {
        $postStr = $post_info;
        $resultStr = "";

        //extract post data
        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            return trim($postObj->MsgType);
        } else {
            return "";
        }
    }

    public function responseUserMsg($post_info) {
        $resp_msg = $this->responseMsg($post_info);
        $postObj = simplexml_load_string($post_info, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$postObj) {
            return;
        }
        $user_open_id = (string) $postObj->FromUserName;
        $user_info = $this->getUserInfo($user_open_id);

        return array('rsp_msg' => $resp_msg, 'user_info' => $user_info);
    }

    /**
     * 主入口处理函数
     *
     * @param string $post_info
     */
    public function responseMsg($post_info = "") {
        $postStr = !empty($post_info) ? $post_info : file_get_contents("php://input");

        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $msgType = trim($postObj->MsgType);
            $content = '功能暂未开放';
            switch ($msgType) {
                case 'text':
                    $this->handleText($postObj);
                    break;
                case 'event':
                    $this->handleEvent($postObj);
                    break;
                default: //图片（image）语音（voice）视频为video 小视频为shortvideo   消息类型（link）
                    //$this->handleStr($postObj, $content);
                    break;
            }
        }
    }

    /**
     * 处理回复消息字符串
     *
     * @param SimpleXMLElement $obj
     * @param string $content
     */
    private function handleStr($obj, $content) {
        $create_time = time();
        $user_id = (string) $obj->FromUserName;
        $to_user = (string) $obj->ToUserName;

        echo <<<XML
<xml>
    <ToUserName><![CDATA[{$user_id}]]></ToUserName>
    <FromUserName><![CDATA[{$to_user}]]></FromUserName>
    <CreateTime>{$create_time}</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[{$content}]]></Content>
</xml>
XML;
    }

    //回复文本消息
    public function handleText($obj) {
        $create_time = time();
        $user_id = (string) $obj->FromUserName;
        $to_user = (string) $obj->ToUserName;
        $content = $obj->Content ? trim($obj->Content) : "";

        $reply = '';

        //新需求接入
        $sessionKey = "yib:msg:" . SITE_ID . ':' . $user_id;
        $stateKey = 'yib:msg:state:' . SITE_ID . ":" . $user_id;
        $sessionFlag = $this->redis->get($sessionKey);

        if ($sessionFlag != false) {

            //用户输入Q 退出会话
            if (strtolower($content) == 'q') {
                $this->redis->del($sessionKey);
                $this->redis->del($stateKey);
                //API call : send quit session command 
                $response = $this->sendQuidCommand($user_id);
                $this->logger->debug("API MSG QUIT RESPONSE:" . json_encode($response));
                return $this->sendTextMsg($user_id, $to_user, $create_time, self::MSG_END);
            }

            //调用API,将数据发送给API
            $this->sendAppMessage($user_id, $content);
            $responseState = $this->redis->incr($stateKey);

            if ($responseState >= 3) {
//                return $this->sendTextMsg($user_id, $to_user, $create_time, '药师正忙,请耐心等待哦!');
            }
            //返回空,这一行非常重要!
            echo '';
            exit;
        }
        //走普通流程
        else {
            //将原来的回复从配置种获取改为=> 读取接口回复
            //获取关键字,如果获取到了,则回复相应的内容
            $response = $this->api->request("weixin/getByKeyword", array("keywords" => $content));
            if (isset($response['status']) && $response['status'] && !empty($response['result']['reply_content'])) {
                $reply = $response['result']['reply_content'];
            } else {
                //未匹配到得关键字回复
                $response = $this->api->request("weixin/getAutoReplyList", array("reply_type" => 120));
                if (isset($response['status']) && $response['status']) {
                    $reply = $response['result']['items'][0]['reply_content'];
                }
            }

            if ('' == $reply) {
                echo '';
                exit;
            }
        }

        return $this->sendTextMsg($user_id, $to_user, $create_time, $reply);
    }

    private function sendTextMsg($user_id, $to_user, $create_time, $reply) {
        $textTpl = "<xml>
        <ToUserName><![CDATA[{$user_id}]]></ToUserName>
        <FromUserName><![CDATA[{$to_user}]]></FromUserName>
        <CreateTime>{$create_time}</CreateTime>
        <MsgType><![CDATA[text]]></MsgType>
        <Content><![CDATA[%s]]></Content>
        </xml>";
        echo sprintf($textTpl, $reply);
        exit;
    }

    //处理事件消息
    private function handleEvent($obj) {
        $user_id = (string) $obj->FromUserName;
        $toUser = (string) $obj->ToUserName;
        $content = $obj->Content ? trim($obj->Content) : "";
        $event_key = isset($obj->EventKey) && $obj->EventKey ? $obj->EventKey : "";

        //$config = $this->config->item->wechat;
        switch (strtolower(($obj->Event))) {
            case 'subscribe':
                $this->delCachedUserInfo($user_id);
                //记录用户来源缓存 => 店员/门店 扫码
                $this->setUserSourceCache($event_key, $user_id);
                
                $siteWeChatSubscribeConfig = config('wechat.event.subscribe');
                $this->logger->debug(var_export($siteWeChatSubscribeConfig, true));
                if ($siteWeChatSubscribeConfig && ($siteWeChatSubscribeConfig['type'] == 'news' || $siteWeChatSubscribeConfig['type'] == 'text')) {
                    $this->responseNews($obj, $siteWeChatSubscribeConfig['content'], $siteWeChatSubscribeConfig['type']);
                    return;
                }
                $content .= "欢迎关注该公众号!";
                //将原来的回复从配置种获取改为=> 读取接口回复
                $response = $this->api->request("weixin/getAutoReplyList", array("reply_type" => 110));
                if ($response['status']) {
                    $content = $response['result']['items'][0]['reply_content'];
                }
                $this->responseNews($obj, '<Content><![CDATA[' . $content . ']]></Content>', 'text');
                /*
                  $open_id = strval($obj->FromUserName);
                  $userInfo = $this->getUserInfo($open_id);
                  if (!$userInfo || !count($userInfo)){
                  $this->logger->debug("ERR: failed to get user info via open id: " . $open_id);
                  return false;
                  }

                  $userInfo['open_id'] = $open_id;
                  $syncResponse = $this->api->request("user/updateWechatInfo", ['info' => json_encode($userInfo) ]);
                  $this->logger->debug("SYNC result : " . json_encode($syncResponse));
                 */
                return;
            case 'scan': //已关注过服务号，通过二维码进来时
                $content .= "欢迎关注该公众号!";
                //判断门店相关信息，可以绑定门店和会员
                if ($event_key) { //store_id
                    $store_id = $event_key;
                    //TODO LIST
                }

                break;
            case 'unsubscribe':
                $content .= "感谢您一直以来对该公众号的关注,再见!";
                $this->delCachedUserInfo($user_id);
                break;
            case 'location': //地理位置 将用户的位置放进redis
                $lat = (string) $obj->Latitude;
                $lng = (string) $obj->Longitude;
                $Precision = (string) $obj->Precision;
                $this->logger->debug(__CLASS__ . "user+++++ {$user_id} location:" . var_export($lat, true) . var_export($lng, true));
                $this->setUserLocationRedis($user_id, $lat, $lng, $Precision); //更新缓存

                $content = "";
                break;
            case 'view': //点击菜单跳转链接时的事件推送----
                $key = (string) $obj->EventKey;
                $content = "";
                break;
            case 'click': //点击菜单跳转链接时的事件推送----
                $key = (string) $obj->EventKey;
                $content = "";
//                file_put_contents('/tmp/test.log', $key,FILE_APPEND);
                if (in_array(trim($key), array('V1002_DISCOUNT', 'V1002_KNOWLEDGE', 'V1002_INTRODUCE',))) {
                    $content = "功能暂未开放，请关注后续动态，谢谢！";
                }

                $stateKey = 'yib:msg:state:' . SITE_ID . ":" . $user_id;

                if ($key == 'V1002_MSG') {
                    $sessionKey = "yib:msg:" . SITE_ID . ':' . $user_id;
                    $session = $this->redis->get($sessionKey);
                    if ($session) {
                        $content = self::MSG_IN_SESSION;
                    } else {
                        $response = $this->registerAppUser($user_id);
                        $this->logger->debug("APP REG RESPONSE:" . var_export($response, 1));
                        $this->redis->setex($sessionKey, 3600 * 48, 'session_start');
                        $this->redis->setex($stateKey, 3600, 1);
                        $content = self::MSG_START;
                    }
                }
                break;

            default:
                //$content .= "";
                $content = "";
                break;
        }
        $this->handleStr($obj, $content);
    }

    /**
     * 获取关注者信息
     *
     * @param string $user_open_id
     * @return array
     */
    public function getUserInfo($openID) {
        $userInfo = $this->redis->get(CacheConst::getUserInfoKey($openID));
        if (!$userInfo) {
            return [];
        }

        return json_decode($userInfo, 1);
    }

    public function setUserInfo($openID) {
        //校验用户信息是否获取过,如果没有,根据拿到的OPENID以及TOKEN获取用户信息
        $token = $this->generateToken(true);

        $userInfo = $this->getUserInfo($openID);
        if (!$userInfo) {
            $infoURL = config('common.weiXinBaseUrl') . "/cgi-bin/user/info?access_token=" . $token . "&openid=" . $openID . "&lang=zh_CN";
            $userInfo = $this->curl($infoURL);
            $userInfo = json_decode($userInfo, 1);
            if (isset($userInfo['errcode'])) {
                $this->logger->error("ERR ON WEIXIN API" . json_encode($userInfo));
            }
            $this->logger->debug("User Auth Response: " . json_encode($userInfo));
            $this->redis->setex(CacheConst::getUserInfoKey($openID), 24*60*60, json_encode($userInfo));

            return $userInfo;
        }

        return json_decode($userInfo, 1);
    }

    /**
     * 删除缓存的用户信息
     *
     * @param string $user_open_id
     * @return int
     */
    public function delCachedUserInfo($user_open_id) {
        return $this->redis->del(sprintf(self::WX_USER_INFO_KEY_FMT, $user_open_id));
    }

    /**
     * 发送客服消息
     * @param $open_id
     * @param $type 1:text;2:image;3:voice;4:video;5:music;6:news;
     * @param $content
     */
    public function sendCustomMessage($open_id, $type = 1, $content) {
        if (!$open_id || empty($type) || empty($content)) {
            return array('code' => -1100, 'msg' => '参数不完整');
        }
        $array_type = array(1 => 'text', 2 => 'image', 3 => 'voice', 4 => 'video', 5 => 'music', 6 => 'news');
        if (!in_array($type, array_keys($array_type))) {
            return array('code' => -1200, 'msg' => '错误的消息类型');
        }

        $access_token = self::generateToken();
        $response_url = config('common.weiXinBaseUrl') . "/cgi-bin/message/custom/send?access_token={$access_token}";
        $touser = $open_id;
        $msgtype = $array_type[$type];
        $text_content = $content;
        $rs_text = '{
            "touser":"' . $touser . '",
            "msgtype":"' . $msgtype . '",
            "text":{"content":"' . $text_content . '"}
        }';

        $strData = $this->api->post($response_url, $rs_text);
        $strData = json_decode($strData, 1);
        return array('code' => $strData['errcode'], 'msg' => $strData['errmsg']);
    }

    /**
     * 获取关注者列表
     */
    public function getUserList($star = 0) {
        $access_token = self::generateToken();
        $url_user = config('common.weiXinBaseUrl') . "/cgi-bin/user/get?access_token={$access_token}&next_openid={$star}";
//        $user_rs = CurlClient::getInstance()->doGet($url_user);
        $user_rs = $this->api->get($url_user);
        return json_decode($user_rs, true);
    }

    // 开发者通过检验signature对请求进行校验
    private function checkSignature() {
        $signature = isset($_GET["signature"]) ? $_GET["signature"] : '';
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';

        $token = config('common.token');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    public function getUserBaiduLocation($user_id) {
        $localUser = $this->getUserLocationRedis($user_id);

        if (!empty($localUser)) {
            return $this->changeBaiduMap($localUser['lat'], $localUser['lng']);
        } else {
            return [];
        }
    }

    public function getUserLocationRedis($openID) {
        $key = 'yib:user:location:' . SITE_ID . ':' . $openID;
        $data = $this->redis->get($key);

        if (!$data) {
            return [];
        }

        return json_decode($data, true);
    }

    public function add_template_id($token, $id = "TM00015") {
        $key = "yib:site:template:msg:" . SITE_ID;
        $template_id = $this->redis->get($key);
        if ($template_id) {
            return $template_id;
        }

        $apiURL = "https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token=" . $token;
        $this->logger->debug("API URL : " . $apiURL);
        $response = $this->api->curlPost($apiURL, '{"template_id_short":"' . $id . '"}');
        if (!$response) {
            $this->logger->error("Failed to request api ");
            return false;
        }

        $response = json_decode($response, 1);
        if ($response['errcode'] != 0) {
            $this->logger->error("Error on request api :  " . var_export($response, 1));
            return false;
        }
        
        $this->redis->set($key,$response['template_id']);
        return $response['template_id'];
    }

    public function templateMsg($dataHash = array()) {
        $token = $this->redis->get("yib:site:token:" . SITE_ID);
        if (!$token) {
            $this->logger->error("Failed to get site token ");
            return false;
        }

           
        $templateID = $this->add_template_id($token);
        if(false === $templateID) {
            $this->logger->error("Failed to get template id ");
            return false;
        }

        $url = config("common.baseDomain");
        $url = "http://" . $url . "/order/details/" . $dataHash['trades_id'];
        $params = '{ "touser":"%s", "template_id":"%s", "url":"%s", "data":{ "first": { "value":"%s", "color":"#173177" }, "orderMoneySum":{ "value":"%s", "color":"#173177" }, "orderProductName": { "value":"%s", "color":"#173177" }, "Remark":{ "value":"%s", "color":"#173177" } } }';
        $params = sprintf($params, $dataHash['open_id'], $templateID, $url, '恭喜您购买成功', number_format($dataHash['real_pay'] / 100, 2) . "元", $dataHash['trades_title'], '欢迎下次继续购买');

            
        $response = $this->api->curlPost("https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $token, $params);
        if (!$response) {
            $this->logger->error("Failed to request api ");
            return false;
        }

        $response = json_decode($response, 1);
        if ($response['errcode'] != 0) {
            $this->logger->error("Error on request api :  " . $response['errmsg']);
            return false;
        }
        $this->logger->debug("Template msg  params:" . $params);
        $this->logger->debug("Template msg  response:" . var_export($response, 1));

        return true;
    }

    /**
     * 
     * 微信通过主动通知接口,将用户当前的经度,维度,以及精度通知到接口,获取到这些数据后
     * 存储在REDIS内,应当接收到一次并立即更新一次,以获取最新的坐标!
     * @param type $user_id
     * @param type $lat
     * @param type $lng
     * @param type $Precision
     * @return boolean
     */
    private function setUserLocationRedis($openID = '', $lat = '', $lng = '', $Precision = '') {
        $key = 'yib:user:location:' . SITE_ID . ':' . $openID;
        $dataHash = ['lat' => $lat, 'lng' => $lng, 'precision' => $Precision];
        $this->redis->set($key, json_encode($dataHash));
        $this->logger->debug("User location:" . json_encode($dataHash));
        return $dataHash;
    }

    //若确认此次GET请求来自微信服务器，请原样返回echostr参数内容，则接入生效，否则接入失败。
    public function valid() {
        $echoStr = $_GET["echostr"];
        //valid signature , option
        $rs = $this->checkSignature();

        if ($rs) {
            echo $echoStr;
            exit;
        }
    }

    /*     * 地图坐标转换
     * @param $lat
     * @param $lng
     * @return array|void
     */

    public function changeBaiduMap($lat, $lng, $type = 1) {
        $ak = config('common.baiduApiKey');
        $qChBaiduUrl = "http://api.map.baidu.com/geoconv/v1/?coords={$lng},{$lat}&from={$type}&to=5&ak={$ak}";
//        $rs = CurlClient::getInstance()->doGet($qChBaiduUrl);
        $rs = $this->api->get($qChBaiduUrl);
        $resultChBaidu = json_decode($rs, true);
        //LoggerFactory::getLogger(__CLASS__)->debug("changeBaiduMap:" . $rs);
        $lat = $resultChBaidu['result'][0]['y'];
        $lng = $resultChBaidu['result'][0]['x'];
        return array('lat' => $lat, "lng" => $lng);
    }

    /**
     * 根据地址获取百度
     *
     * @param string $address
     * @return array
     */
    public function getGeoLocation($address) {
        if (empty($address)) {
            return [];
        }
        $params = [
            'output' => 'json',
            'ak' => config('common.baiduApiKey'),
            'address' => $address
        ];
        $apiURL = 'http://api.map.baidu.com/geocoder/v2/?' . http_build_query($params);
        $this->logger->debug($apiURL);
        $rs = $this->api->get($apiURL);
        $this->logger->debug($rs);
        $result = json_decode($rs, true);
        if ($result['status'] == 0) {
            return $result['result']['location'];
        }
        return [];
    }

    /**
     * @param $baiduLocation
     * @param array $arrParam 说明  查询参数说明 http://172.20.10.246:9020/searcher/getDistance?start=0&limit=100&indexName=ybzf_store&appkey=appkey
     * &fl=id,name,address&q=name:(王州)&coordinate=39.931627,116.40589&fq=id:[\806181 TO \906181]&facet=[{"appendFields":"","groupbyFieldName":"city_id","limit":10,"operate":"count","operateFieldName":"id","sort":"DESC"}]
     * 1、start,limit，相当于mysql的分页功能; 2、indexName 索引的名字;  3、appkey暂时不启用，用于以后接口的安全调用控制
     * 4、fl,指定要返回的字段，不指定全返回，类似于mysql的select功能，q=name:(王州) name:(_) OR id:233
     * 5、q,搜索条件。如：name:(王州) name:(_) OR id:233
     * 6、fq，过滤条件，类似于mysql的where功能，如： fq=id:[\806181 TO \906181] city:[\222 to \666]
     *
     * 以下是分组参数facet说明,在搜索中实现sql的分组统计功能,&facet=[{"appendFields":"","groupbyFieldName":"city_id","limit":10,"operate":"count","operateFieldName":"id","sort":"DESC"}]
     * 1、appendFields，暂时不用，预留字段
     * 2、groupbyFieldName，分组字段
     * 3、operate，相当于mysql中的聚合函数，目前支持count,avg,sum
     * 4、operateFieldName，operate操作的字段
     * 5、sort,排序方式，asc,desc
     * 根据用户的地理位置获取店铺列表
     * @param int $groupByFlag
     * @return mixed
     */
    public function getBaiduMapLocationInfo($baiduLocation, $arrParam, $groupByFlag = 0) {
        $start = !empty($arrParam['start']) ? $arrParam['start'] : 0;
        $limit = !empty($arrParam['limit']) ? $arrParam['limit'] : 100;
        $indexName = !empty($arrParam['indexName']) ? $arrParam['indexName'] : 'stores_' . SITE_ID;
        $appkey = !empty($arrParam['appkey']) ? $arrParam['appkey'] : 'appkey';
        $fl = !empty($arrParam['fields']) ? $arrParam['fields'] : 'id,name,address';
        $fq = !empty($arrParam['fq']) ? $arrParam['fq'] : '';
        $q = !empty($arrParam['where']) ? $arrParam['where'] : '';
        $groupField = !empty($arrParam['group_field']) ? $arrParam['group_field'] : 'city_id';
        $lat = isset($baiduLocation['lat']) ? $baiduLocation['lat'] : 0;
        $lng = isset($baiduLocation['lng']) ? $baiduLocation['lng'] : 0;

        /*
          if ($lat && $lng  ){
          //根据坐标获取城市和省份名称
          $ak = config('common.baiduApiKey');
          $urlCityInfo = "http://api.map.baidu.com/geocoder?location={$lat},{$lng}&output=json&key={$ak}";
          //$rs = CurlClient::getInstance()->doGet($urlCityInfo);
          $rs = $this->api->get($urlCityInfo);
          $resultCityInfo = json_decode($rs, true);
          $rs_city_name = $resultCityInfo['result']['addressComponent']['city'];
          $rs_province_name = $resultCityInfo['result']['addressComponent']['province'];
          if ($rs_city_name && $rs_province_name){
          $q .= urlencode(" province_name:({$rs_province_name}) city_name:({$rs_city_name}) ");
          }
          } */

        $search_url = WX_SEARCH_API_URL;
        $search_url .= "&start=" . $start;
        $search_url .= "&limit=" . $limit;
        $search_url .= "&indexName=" . $indexName;
        $search_url .= "&appkey=" . $appkey;
        $search_url .= "&fl=" . $fl;
        $search_url .= $q ? "&q=" . $q : "";
        $search_url .= $fq ? "&fq=" . urlencode($fq) : "";
        $search_url .= ($lat && $lng) ? "&coordinate=" . $lat . "," . $lng : "";
        //需要group by的时候
        if ($groupByFlag) {
            $search_url .= '&facet=[{"appendFields":"city_name","groupbyFieldName":"' . $groupField . '","limit":100,"operate":"count","operateFieldName":"id","sort":"DESC"}]';
        }
//        $results = CurlClient::getInstance()->doGet($search_url);

        $this->logger->debug("SEARCH URL:" . $search_url);
        $results = $this->api->get($search_url);
        $this->logger->debug("SEARCH RESULT:" . strval($results));
        return json_decode($results, true);
    }

    //请求baidu的API
    /**
     * $orign = array($lat, $lng, $name='') $destination = array($lat, $lng, $name="")
     */
    public function nava_for_baidu_map($origin = array(), $destination = array(), $region = '上海') {
        $url = 'http://api.map.baidu.com/direction?';
        $origin['lat'] = $origin['lat'] ? $origin['lat'] : '121.48';
        $origin['lng'] = $origin['lng'] ? $origin['lng'] : '31.22';
        $url.= ("origin=latlng:{$origin['lat']},{$origin['lng']}|name:{$origin['name']}&destination=latlng:{$destination['lat']},{$destination['lng']}|name:{$destination['name']}&mode=walking&output=html&region=" . $region);
        return $url;
    }

    /**
     * 根据店铺id生成永久二维码
     */
    public function setStoreQrcode($store_id) {
//        $storeKeyPre = "yib:site:store:code:" . SITE_ID . ':' . $store_id;
        
        $storeKeyPre = CacheConst::getStoreClerkGrcodeKey($store_id);
        $wx_token = self::generateToken();
        $url = config('common.weiXinBaseUrl') . "/cgi-bin/qrcode/create?access_token=" . $wx_token;
        $post_info = array(
            'action_name' => 'QR_LIMIT_SCENE',
            'action_info' => array(
                'scene' => array('scene_id' => $store_id)
            ),
        );
//        $str = CurlClient::getInstance()->doPost($url, json_encode($post_info));
        $str = $this->api->post($url, json_encode($post_info));
        $rs = json_decode($str, true);
        if ($rs && $rs['ticket'] && $rs['url']) {
            $this->redis->set($storeKeyPre, json_encode($rs));
        }
        return $rs;
    }

    /**
     * 获取门店二维码
     */
    public function getStoreQrcode($store_id = 0) {
//        $storeKeyPre = "yib:site:store:code:" . SITE_ID . ':' . $store_id;
        $storeKeyPre = CacheConst::getStoreClerkGrcodeKey($store_id);
        $cacheStr = $this->redis->get($storeKeyPre);
        $ticketInfo = json_decode($cacheStr, true);
        if (!isset($ticketInfo['ticket']) || !isset($ticketInfo['url'])) {
            $ticketInfo = $this->setStoreQrcode($store_id);
        }
        //read from db or redis
        if (!$ticketInfo['ticket']) {
            return false;
        }
        $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=" . urlencode($ticketInfo['ticket']);
        return $url;
    }

    /**
     * 响应图文信息
     *
     * @param $request
     * @param string $content
     * @param string $type
     */
    private function responseNews($request, $content, $type = 'text') {
        $time = time();
        echo <<<XML
<xml>
    <ToUserName><![CDATA[{$request->FromUserName}]]></ToUserName>
    <FromUserName><![CDATA[{$request->ToUserName}]]></FromUserName>
    <CreateTime>{$time}</CreateTime>
    <MsgType><![CDATA[{$type}]]></MsgType>
    {$content}
</xml>
XML;
    }

    /**
     * 聊天需求中, 所有用户都需要注册,因此,第一次开始的时候,推送注册,
     * 这个需求可以做异步操作,暂时没时间,先做同步!
     * @author Robanlee@gmail.com
     */
    public function registerAppUser($openID) {
        //获取用户微信公众号信息
        $userData = $this->redis->get(CacheConst::getUserInfoKey($openID));
        if (!$userData) {
            $this->setUserInfo($openID);
        }

        $userData = $this->redis->get(CacheConst::getUserInfoKey($openID));
        if (!$userData) {
            return [];
        }

        $userData = json_decode($userData, 1);

        //获取用户注册信息(信息来自本站)
        $cacheKey = CacheConst::getUserInfoKey($openID);
        $regData = $this->redis->get($cacheKey);
        if ($regData) {
            $regData = json_decode($regData, 1);
            $userData = array_merge($regData, $userData);
        }


        $params = [
            'phone' => isset($userData['phone']) ? $userData['phone'] : '',
            'open_id' => $openID,
            'nickname' => $userData['nickname'],
            'head_img_url' => $userData['headimgurl'],
            'gender' => $userData['sex'],
            'site_id' => SITE_ID,
            'id_subscribe' => $userData['subscribe'],
            'subscribed_time' => $userData['subscribe_time'],
            'group_id' => $userData['groupid'],
            'country' => $userData['country'],
            'province' => $userData['province'],
            'city' => $userData['city']
        ];

        return $this->apiRequest('wx/users', $params);
    }

    public function sendQuidCommand($openID) {
        $params = ['open_id' => $openID, 'site_id' => SITE_ID];
        return $this->apiRequest('wx/quit', $params);
    }

    public function sendAppMessage($openID, $content) {
        //获取用户坐标
        $userLocation = $this->getUserLocationRedis($openID);

        $params = [
            'open_id' => $openID,
            'msg_type' => 'text',
            'content' => $content,
            'site_id' => SITE_ID,
            'lat' => isset($userLocation['lat']) ? $userLocation['lat'] : '0.0',
            'lng' => isset($userLocation['lng']) ? $userLocation['lng'] : '0.0',
        ];

        return $this->apiRequest('wx/messages', $params);
    }

    private function apiRequest($api, $params) {
        $apiRUL = config('common.mobileApi');
        $apiRUL = $apiRUL . $api;

        $params = json_encode($params);
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'timeout' => '10',
                'content' => $params
            )
        );
        
        $this->logger->debug("APP API PARAMS(" . $apiRUL . "):" . var_export($opts, 1));
        
        $context = stream_context_create($opts);
        $buff = @file_get_contents($apiRUL, false, $context);
        
        $this->logger->debug("APP API RESPONSE DEBUG (" . $apiRUL . "):" . var_export($buff, 1));
        
        if (!$buff) {
            return [];
        }
        $this->logger->debug("APP API RESPONSE(" . $apiRUL . "):" . var_export($buff, 1));
        return json_decode($buff, 1);
    }
    /**
     * 设置用户来源缓存
     * @param type $event_key
     * @param type $openID
     * @return boolean
     */
    public function setUserSourceCache($event_key, $openID) {
        if (!$event_key) {
            return false;
        }
        $arr = explode('_', ((string) $event_key));
        $clerk_id = intval($arr[count($arr)-1]);
        $this->setStoreClerkIdByOpenId($clerk_id, $openID);
    }
    /**
     * 设置店员/门店id
     * @param type $clerk_id
     * @param type $openID
     * @return boolean
     */
    public function setStoreClerkIdByOpenId($clerk_id, $openID) {
        if (!$clerk_id) {
            return false;
        }
        $this->redis->set(CacheConst::getStoreClerkIdKey($openID), $clerk_id);
    }
    /**
     * 获取店员/门店ID
     * @param type $openID
     * @return type
     */
    public function getStoreClerkIdByOpenId($openID) {
        return $this->redis->get(CacheConst::getStoreClerkIdKey($openID));
    }
    /**
     * 删除缓存
     * @param type $openID
     * @return type
     */
    public function delStoreClerkIdByOpenId($openID) {
        return $this->redis->del(CacheConst::getStoreClerkIdKey($openID));
    }
}
