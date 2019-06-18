<?php

namespace Bloomfilter;
use Exception;
use Redis;
class BloomFilterRedis
{
    protected $hash;
    protected $redis;
    protected $bucket = 'bloomFilter';
    protected $hashFunction = array('BKDRHash', 'SDBMHash', 'JSHash');

    public function __construct($host = '127.0.0.1',$password = null,$port = '6379')
    {
        if (!$this->bucket || !$this->hashFunction) {
            throw new Exception("需要定义bucket和hashFunction", 1);
        }
        $this->hash = new BloomFilterHash;
        $this->redis = new Redis();
        //连接
        $this->redis->connect($host,$port);
        //验证
        $this->redis->auth($password);
    }

    /**
     * 添加到集合中
     * @param string $string
     */
    public function add($string)
    {
        //开始事务
        $pipe = $this->redis->multi();
        foreach ($this->hashFunction as $function) {
            $hash = $this->hash->$function($string);
            $pipe->setbit($this->bucket, $hash, 1);
        }
        //执行
        return $pipe->exec();
    }

    /**
     * 查询是否存在, 存在的一定会存在, 不存在有一定几率会误判
     * @param string $string
     * @return mixed
     */
    public function exists($string)
    {
        //开始事务
        $pipe = $this->redis->multi();
        $len = strlen($string);
        foreach ($this->hashFunction as $function) {
            $hash = $this->hash->$function($string, $len);
            $pipe = $pipe->getbit($this->bucket, $hash);
        }
        //执行
        $res = $pipe->exec();
        foreach ($res as $bit) {
            if ($bit == 0) {
                return $this->toJson(404,'不存在',0);
            }
        }
        return $this->toJson(0,'已存在',1);
    }

    public function toJson($code = 0,$msg = '',$data = ''){

        $result['code'] = $code;
        $result['msg'] = $msg;
        $result['data'] = $data;
        return $result;
    }
}