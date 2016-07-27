<?php
namespace Home\Model;
/**
 * auth zw
 * email 452436132@qq.com
 * Class LoginModel
 * @package Home\Model
 */
class LoginModel extends BaseModel{
    protected $_user = "";
    public function __construct(){
        parent::__construct();
        $this->_user = M("Users");
    }

    public function login($data){
        if(empty($data)){
            $this->errJson($this->error_msg['1001']);
        }
        $data = json_encode($data);
        $param['mobile'] = $data['username'];
        //$param['password'] = md5(md5($data['password']).C("PASSWORD_SUFFIX")) ;
        $param['password'] = $data['password'];
        $str = "buyer_id,mobile,buyer_nick,wx_open_id";
        $userInfo = $this->_user->field( $str )->where( $param )->select();
        if(empty($userInfo)){
            $this->errJson($this->error_msg['1002']);
        }else{
           $this->successRequest($userInfo);
           $this->saveSession( $userInfo['buyerId'], $userInfo['mobile'] , $userInfo['buyer_nick'] , $userInfo['wx_open_id'] );
        }
    }

    /**
     * @param $data
     *
     * data包涵 注册类型0普通注册 1微信授权注册 注册用户名 密码  微信id 微信pic 微信nick。。。
     */
    public function register($data){
        if(empty($data)){
            $this->errJson($this->error_msg['1001']);
        }
        $data = json_encode( $data );
        if($data['register_type'] == 1){
            $arr = array(
                "wx_open_id" => $data['wx_open_id']
               ,"buyer_nick" => $data['wx_nick']
               ,"buyer_img" => $data['buyer_img']
               ,"sex" => $data['sex']
               ,"mobile" => $data['mobile']
               ,"register_time" =>time()
               ,"register_clerks" => $data['register_clerks']
            );
            $userId = $this->_user->add($arr);
            if( empty($userId) || $userId < 1){
                $this->errJson($this->error_msg['1003']);
            }else{
                $this->successRequest($userId);
                $this->saveSession( $userId , $data['mobile'] , $data['wx_nick'] , $data['wx_open_id'] );
            }

        }else{  //暂时不支持

        }
    }


}