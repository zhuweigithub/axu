<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function __construct()
    {
     parent::__construct();
    }
    public function index(){
        fb($_SERVER);
        $arr=array(
            "hello"=>2323,
            "word"=>1111,
            "ssssss"=>222,
        );
        $this->setRedis("zw",$arr);
        $this->display();
        }
    public function getList(){
        $aa=$this->getRedis("zw");
        fb($aa);
        $this->display();
    }
}