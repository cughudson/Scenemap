<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/3/24
 * Time: 0:10
 */
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
session_start(['cookie_lifetime' => 30 * 60]);
header("Content-Type:text/html; charset=utf8");
if ($_SERVER["REQUEST_METHOD"] == 'GET') die();

if (!array_key_exists("login", $_SESSION)) {
    echo "<script type='text/javascript'>alert('检测到您尚未登陆系统，请登陆')</script>";
    echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
    die();
}
if (isset($_SESSION['time'])) {
    if ($_SESSION['time'] < time()) {
        session_unset();
        session_destroy();
        echo "<script type='text/javascript'>alert('会话已经过期，如需继续操作，请重新登陆')</script>";
        echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}
$city = $_POST["city"];
//两年后过期
setrawcookie("city", $city, time() + 2 * 365 * 24 * 60 * 60, "/");
$respond = array("status" => true);

echo json_encode($respond);