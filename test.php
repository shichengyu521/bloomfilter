<?php
require_once './vendor/autoload.php';
use Bloomfilter\BloomFilterRedis;
$bloomFilter = new BloomFilterRedis();
$bloomFilter->add('张三');
$bloomFilter->add('李四');
$bloomFilter->add('王五');
$bloomFilter->add('赵柳');
$result = $bloomFilter->exists('李四');
echo $result;
