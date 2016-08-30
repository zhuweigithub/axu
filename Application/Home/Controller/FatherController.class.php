<?php
namespace Home\Controller;
use Think\Controller;
class FatherController extends Controller {

    protected $userInfo = '';
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

}