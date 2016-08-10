<?php
namespace Home\Controller;
use Think\Cache\Driver\Redis;
use Think\Controller;
class BaseController extends Controller {
    public $redis=null;
    protected $cacheSwitch;
    protected $cachePrefix;
    protected $redisTime;
    public function __construct()
    {
        parent::__construct();
        $this->redis=new Redis();
        //REDIS缓存前缀
        $this->cachePrefix = "axu";
        //缓存开关 为空则时关闭redis
        $this->cacheSwitch = CACHE_REDIS_SWITCH;
        $this->redisTime=C(REDIS_TIME);
    }

    /**获取redis
     * @param $key
     * @return bool|mixed
     */
    public function getRedis($key){
        if(!$this->cacheSwitch){
            return false;
        }
        $data = $this->redis->get($key);
        if ($data === false) {
            return false;
        }
        return $data;
    }

    /** 设置redis
     * @param $key
     * @param $value
     * @param string $data
     * @return bool
     */
    public function setRedis($key,$value,$data =""){
        if(!$this->cacheSwitch){
            return false;
        }
        $data=$this->redisTime;
        fb($key);
        fb($value);
        return $this->redis->set($key,$value,$data);

    }

    /** 根据key删除redis
     * @param $key
     * @return bool
     */
    public function rmRedis($key){
        return $this->redis->rm($key);
    }

    /** 清除redis
     * @return bool
     */
    public function clearRedis(){
        return $this->redis->clear();
    }

}