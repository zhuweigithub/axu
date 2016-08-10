<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function __construct()
    {
     parent::__construct();
    }
    public function index(){

        $this->display();
        }
    public function getList(){

        $this->display();
    }
    public function checkSignature()
    {
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = "zhuwei";
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            $echoStr = $_GET["echostr"];
            echo $echoStr;
            return true;
        }else{
            return false;
        }
    }
}