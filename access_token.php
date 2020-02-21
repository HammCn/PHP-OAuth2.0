<?php
//引入Mysql配置并连接
require_once("mysql.php");
header('Content-type: application/json'); 
//连接数据库
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    echo json_encode([
        'code'=>500,
        'msg'=>$conn->connect_error
    ]);
    die;
} 
//参数判断开始
if(empty($_REQUEST['code'])){
    echo json_encode([
        'code'=>404,
        'msg'=>'参数code丢失'
    ]);
    die;
}
$code = htmlspecialchars($_REQUEST['code']);


if(empty($_REQUEST['appid'])){
    echo json_encode([
        'code'=>404,
        'msg'=>'参数appid丢失'
    ]);
    die;
}
$appid = htmlspecialchars($_REQUEST['appid']);

if(empty($_REQUEST['secret'])){
    echo json_encode([
        'code'=>404,
        'msg'=>'参数secret丢失'
    ]);
    die;
}
$secret = htmlspecialchars($_REQUEST['secret']);
//参数判断结束

//查询app 判断appid secret是否有效
$sql = "select id from app where appid like '{$appid}' and secret like '{$secret}' limit 0,1";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    echo json_encode([
        'code'=>500,
        'msg'=>'应用appid或secret错误'
    ]);
    die;
}
$app = $result->fetch_assoc();

//查询code是否有效
$sql = "select user,time from code where code like '{$code}' limit 0,1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $code = $result->fetch_assoc();
    //清空所有的code
    $sql = "delete from code where user = {$code['user']}";
    $result = $conn->query($sql);
    if(time() - $code['time'] > 300){
        echo json_encode([
            'code'=>500,
            'msg'=>'code过期'
        ]);
        die;
    }
    //生成新的access_token 自定义算法
    $access_token = sha1(time().rand(1000,9999)).sha1(time());
    $time = time();
    //更新一个access_token并输出
    $sql = "update user set access_token = '{$access_token}',time='{$time}' where id = {$code['user']}";
    $result = $conn->query($sql);
    echo json_encode([
        'code'=>200,
        'access_token'=>$access_token
    ]);
    die;
}else{
    echo json_encode([
        'code'=>500,
        'msg'=>'无效的code'
    ]);
    die;
}