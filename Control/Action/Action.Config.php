<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/10
 * Time: 18:26
 */
session_start(['cookie_lifetime' => 30 * 60 * 24]);
if (!array_key_exists("login", $_SESSION)) {
    echo "<script type='text/javascript'>alert('检测到您尚未登陆系统，请登陆')</script>";
    die();
}
if (isset($_SESSION['time'])) {
    if ($_SESSION['time'] < time()) {
        session_unset();
        session_destroy();
        echo "<script type='text/javascript'>alert('会话已经过期，如需继续操作，请重新登陆')</script>";
        echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
        die();
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}
include_once dirname(__FILE__, 2) . "/Lib/ConfigManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
$filePath = dirname(__FILE__, 3) . "/Config.ini";

$req_type = $_SERVER["REQUEST_METHOD"];
if ($req_type == "GET") {
    die();
}
$data = $_POST['data'];
$config = new ConfigManagment($filePath);
$isOK = $config->Write($data);
if ($isOK != false) {
    $respond = array(
        "status" => true,
    );
    echo json_encode($respond);
    die();
} else {
    $respond = array(
        "status" => false,
    );
    echo json_encode($respond);
    die();
}
