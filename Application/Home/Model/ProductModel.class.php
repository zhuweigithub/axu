<?php
/**
 * Created by zw.
 * User: zw
 * email 452436132@qq.com
 * Date: 2016/10/15
 * Time: 21:30
 */
namespace Home\Model;
class ProductModel extends BaseModel
{
	protected $_activity = "Activitys";

	public function __construct()
	{
		parent::__construct();
	}

	/** type为0则查找所有数据， 为1查找秀图  为2查找秀秘密
	 * @param $data
	 * @return mixed
	 */
	public function getActivityList($data)
	{
		if (empty($data)) {
			$this->errJson($this->error_msg['1001']);
		}
		$param['buyer_id'] = $data['buyer_id'];
		$param['type']     = $data['type'];
		$order             = $data['order'];
		$pageNum           = $data['pageNum'] ? $data['pageNum'] : 10;
		$pageNo            = $data['pageNo'] ? $data['pageNo'] : 1;
		$list              = $this->page($this->_activity, $param, $order, $pageNum, $pageNo);
		return $this->returnSuccessResult($list);
	}

	/**
	 * @param $data
	 *
	 * data包涵 注册类型0普通注册 1微信授权注册 注册用户名 密码  微信id 微信pic 微信nick。。。
	 */
	public function addActivity($data)
	{
		if (empty($data)) {
			$this->errJson($this->error_msg['1001']);
		}
		$data       = json_encode($data);
		$arr        = array(
			"buyer_id"            => $this->_loginMessage['buyerId']
		, "buyer_nick"            => $this->_loginMessage['buyer_nick']
		, "type"                  => $data['type']
		, "activity_name"         => $data['activity_name']
		, "activity_distinct_pic" => $data['activity_distinct_pic']
		, "activity_original_pic" => $data['activity_original_pic'] //得php生成
		, "activity_content"      => $data['activity_content']
		, "create_time"           => time()
		, "start_time"            => $data['start_time']
		, "end_time"              => $data['end_time']
		);
		$activityId = $this->_loginMessage->add($arr);
		if (empty($activityId) || $activityId < 1) {
			$this->errJson($this->error_msg['1005']);
		} else {
			$this->successRequest($activityId);
		}
	}


}