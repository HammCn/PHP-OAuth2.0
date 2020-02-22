<?php
//引入Mysql配置并连接
require_once("mysql.php");

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 

if(empty($_GET['code'])){
    header("Location: /index.php");
    die;
}
$code = htmlspecialchars($_GET['code']);
$secret = "abcdefg";
$appid = "1000";
$ret = file_get_contents("https://oauth.hamm.cn/access_token.php?code={$code}&appid={$appid}&secret={$secret}");
$obj = json_decode($ret);
if($obj->code!=200){
    header("Location:/index.php");
    die;
}
$access_token = $obj->access_token;


$ret = file_get_contents("https://oauth.hamm.cn/getUserInfo.php?access_token={$access_token}");
print_r($ret);die;
