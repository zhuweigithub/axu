<?php
namespace Common\Controller;
use Think\Controller;
class WxJssdkController extends Controller{

    private $pageNum;
    private $pageNo;

    public function __construct(){
        parent::__construct();

    }

    /** 分页请求函数
     * @param $dbName
     * @param null $where
     * @param $order
     * @param int $pageNum
     * @param int $pageNo
     * @return mixed
     */
    public function page($dbName , $where = null , $order , $pageNum = 10, $pageNo = 1){
        $db = M($dbName);
        $startIndex = ($pageNo - 1) * $pageNum;
        $count = $db->where($where)->count();
        $result = $db->where($where)
            ->order($order . " desc")
            ->limit($startIndex,$pageNum)
            ->select();
        $params['result'] = $result;
        $params['count']  =$count;
        return $params;
    }



}
