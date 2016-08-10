<?php
namespace Home\Model;
/**
 * auth zw
 * email 452436132@qq.com
 * Class ProductModel
 * @package Home\Model
 */
class ProductModel extends BaseModel{
    protected $_activity = "";
    public function __construct(){
        parent::__construct();
        $this->_activity = M("Activitys");
    }

    /**
     * @param $data
     * type为0则查找所有数据， 为1查找秀图  为2查找秀秘密
     */
    public function getActivityList($data){
        $this->loginInfo();
        if(empty($data)){
            $this->errJson($this->error_msg['1001']);
        }
        $data = json_encode($data);
        $param['buyer_id'] = $this->_loginMessage['buyerId'];
        $param['type'] = $data['type'] ;
        $count = $this->_activity($param)->count();  // 计算总数
        $page_no = $data['page_no'];//当前页数
        $page_num_list = $data['page_num_list'];//每页显示多少条数据
        $sum_page = $count/$page_num_list;
        if($count%$page_num_list > 0 ){
            $sum_page = $sum_page + 1;
        }
        if( $page_no >= $sum_page ){
            $this->errJson($this->error_msg['1004']);
        }
        $list =  $this->_activity->where($param)->order('create_time desc')->limit(($page_no - 1) * $page_num_list ,$page_num_list)->select();
         $this->successRequest($list);
    }

    /**
     * @param $data
     *
     * data包涵 注册类型0普通注册 1微信授权注册 注册用户名 密码  微信id 微信pic 微信nick。。。
     */
    public function addActivity($data){
        if(empty($data)){
            $this->errJson($this->error_msg['1001']);
        }
        $data = json_encode( $data );
            $arr = array(
                "buyer_id" =>  $this->_loginMessage['buyerId']
                ,"buyer_nick" => $this->_loginMessage['buyer_nick']
                ,"type" => $data['type']
                ,"activity_name" => $data['activity_name']
                ,"activity_distinct_pic" => $data['activity_distinct_pic']
                ,"activity_original_pic" => $data['activity_original_pic'] //得php生成
                ,"activity_content" => $data['activity_content']
                ,"create_time" =>time()
                ,"start_time" => $data['start_time']
                ,"end_time" => $data['end_time']
            );
            $activityId = $this->_loginMessage->add($arr);
            if( empty($activityId) || $activityId < 1){
                $this->errJson($this->error_msg['1005']);
            }else{
                $this->successRequest($activityId);
            }
    }


}