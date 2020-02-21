<?php
//引入Mysql配置并连接
require_once("mysql.php");
//连接数据库
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("连接失败: " . $conn->connect_error);
} 
//判断参数开始
if(empty($_GET['appid'])){
    die("GET参数appid为必须");
}
if(empty($_GET['redirect'])){
    die("GET参数redirect为必须");
}
$appid = intval($_GET['appid']);
$redirect = urldecode($_GET['redirect']);
//获取参数结束
//查询app信息
$sql = "select name from app where appid = {$appid} limit 0,1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    // 输出数据
    $app = $result->fetch_assoc();
    if(!empty($_POST['account']) && !empty($_POST['password'])){
        $account = htmlspecialchars($_POST['account']);
        $password = htmlspecialchars($_POST['password']);
        //用户登录判断
        $sql = "select id,name from user where account like '{$account}' and password like '{$password}' limit 0,1";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            //帐号密码正确 添加临时code
            $code = sha1(time()).rand(100000,999999).sha1(time());
            //删除之前的所有code
            $sql = "delete from code where user = {$user['id']}";
            $result = $conn->query($sql);
            //添加新的code
            $time = time();
            $sql = "insert into code (user,code,time) values ('{$user['id']}','{$code}','{$time}')";
            $result = $conn->query($sql);
            //重定向到第三方应用 带上code
            header('Location: '.$redirect.'?code='.$code);
            die;
        }else{
            echo '<div><font color=red>帐号或密码错误</font></div>';
        }
    }
} else {
    die("应用不存在");
}
$conn->close();
?>
<form method="POST">
    <div>你正在登录 <font color=orangered><?php echo $app['name'];?></font></div>
    <div><input type="tel" placeholder="请输入帐号" name="account" value="admin"/></div>
    <div><input type="password" placeholder="请输入密码" name="password" value="123456"/></div>
    <div><input type="submit" value="登录并授权"/></div>
    <div>admin 123456</div>
</form>