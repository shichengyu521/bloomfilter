使用方法
use Bloomfilter\BloomFilterRedis;

调用方法
$host = 'redis服务器IP';
$password = 'redis服务器密码';
$port = 'redis端口号';
$bloomFilter = new BloomFilterRedis($host,$password,$port);
$bloomFilter->add('张三');
$bloomFilter->add('李四');
$bloomFilter->add('王五');
$bloomFilter->add('赵柳');
$result = $bloomFilter->exists('李四');

返回数据格式
{
    'code' : 0,
    'msg' : '已存在',
    'data' : 1
}
{
    'code' : 404,
    'msg' : '不存在',
    'data' : 0
}
