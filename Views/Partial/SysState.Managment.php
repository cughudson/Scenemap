<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/4
 * Time: 13:15
 */
session_start(['cookie_lifetime' => 30 * 60 * 24]);
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
        die();
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}
?>
<div class="ui info message positive">
    <p>该功能尚未设计实现</p>
</div>
