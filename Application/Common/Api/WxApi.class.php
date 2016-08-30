<?php
namespace Common\Api;
class WxApi{

    protected $_appId ="";
    protected $_appSecret ="";
    public function __construct()
    {
        $this->_appId = C("APP_ID");
        $this->_appSecret = C("APP_SECRET");
    }

    public function creationQrcode ( $id ){
        $token = session("access_token");
        $url   = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=$token";
        $param['action_name '] = "QR_LIMIT_SCENE";
        $param['action_info ']['scene']['scene_id'] = $id;
        $param = json_encode($param );
        $result = http($url , $param , 'POST');
        return $result;
    }
}