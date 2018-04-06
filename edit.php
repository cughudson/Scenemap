<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/25
 * Time: 18:32
 */
session_start(['cookie_lifetime' => 30 * 60]);
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
$type = $_GET['type'];
if ($type == "hotel") {
    $htmlTitle = "民宿编辑";
} else {
    $htmlTitle = "景点编辑";
}
include_once dirname(__FILE__, 1) . "/Views/Partial/Header.php";
include_once dirname(__FILE__, 1) . "/Helper/ChromePhp.php";
if ($type == "hotel") {
    //include_once dirname(__FILE__, 1) . "/Views/Partial/Hotel.Editor.Edit.php";
    include_once dirname(__FILE__, 1) . "/Views/Partial/Scene.Editor.Edit.php";
} else {
    include_once dirname(__FILE__, 1) . "/Views/Partial/Scene.Editor.Edit.php";
}
?>