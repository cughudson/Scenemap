<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/3
 * Time: 1:11
 */
session_start(['cookie_lifetime' => 30 * 60 * 24]);
if ($_SERVER['REQUEST_METHOD'] == "GET") die();
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
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}
include_once dirname(__FILE__, 2) . "/Lib/SceneManagment.php";

$action = $_POST['action'];
$id = $_POST['id'];
$Scene = new SceneManagment();

switch ($action) {
    case 'pull':
        $temp = $Scene->ToDraft($id);
        if (is_array($temp)) {
            $bool = true;
        } else {
            $bool = false;
        }
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        die();
        break;
    case 'waste':
        $temp = $Scene->ToWaste($id);
        if (is_array($temp)) {
            $bool = true;
        } else {
            $bool = false;
        }
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        die();
        break;
    case 'delete':
        $temp = $Scene->Delete($id);
        if (is_array($temp)) {
            $bool = true;
        } else {
            $bool = false;
        }
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        die();
        break;
    case 'restore':
        $temp = $Scene->Restore($id);
        if (is_array($temp)) {
            $bool = true;
        } else {
            $bool = false;
        }
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        die();
        break;
    default:
        break;
}