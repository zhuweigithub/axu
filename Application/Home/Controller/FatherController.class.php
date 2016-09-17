<?php
namespace Home\Controller;
use Think\Controller;
class FatherController extends Controller {

    protected $userInfo = '';
    protected $userMessage = '';
    public function __construct()
    {
        parent::__construct();
        $this->userInfo = session("userInfo");
    }

    /** 检查是否有授权过
     * @return bool
     */
    public function getUserInfo(){
        if(empty($this->userInfo)){
            return false;
        }else{
            return true;
        }
    }
    public function getUser(){
        $db = M();
        $sql = "select * from ax_users as a ,ax_user_ext as b where a.buyer_id = b.buyer_id and a.status = 0 and a.buyer_id = 2";
        $result = $db->query($sql);
        $this->userMessage = $result;
    }
    protected  function gStr($str)
    {
        $encode = mb_detect_encoding( $str, array('ASCII','UTF-8','GB2312','GBK'));
        if ( !$encode =='UTF-8' ){
            $str = iconv('UTF-8',$encode,$str);
        }
        return $str;
    }
}