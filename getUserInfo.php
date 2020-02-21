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
if(empty($_REQUEST['access_token'])){
    echo json_encode([
        'code'=>404,
        'msg'=>'access_token 丢失'
    ]);
    die;
}
$access_token = htmlspecialchars($_REQUEST['access_token']);

$sql = "select id,name,account,time from user where access_token like '{$access_token}' limit 0,1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if(time() - $user['time']>3600){
        echo json_encode([
            'code'=>500,
            'msg'=>'过期的access_token'
        ]);
        die;
    }else{
        unset($user['time']);
        echo json_encode([
            'code'=>0,
            'userInfo'=>$user
        ]);
        die;
    }
}else{
    echo json_encode([
        'code'=>500,
        'msg'=>'没有查询到用户信息'
    ]);
    die;
}