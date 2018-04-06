<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/4
 * Time: 0:49
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
//   include_once dirname(__FILE__,3)."/Helper/ChromePhp.php";
include_once dirname(__FILE__, 2) . "/Lib/SceneManagment.php";
date_default_timezone_set("Asia/Shanghai");
$Req_Type = $_SERVER['REQUEST_METHOD'];

if ($Req_Type == 'GET') {
    die();
}
$data = $_POST['data'];
$action = $_POST['action'];
try {
    $sceneDB = new SceneManagment();
} catch (Exception $ex) {
    $respond = array(
        'status' => false,
    );
    echo json_encode($respond);
    die();
}
switch ($action) {
    case 'save':
        $data['state'] = 1;
        $data['ctime'] = date("Y-m-d H:i:s");
        $data['uuid'] = $_SESSION['uuid'];
        $status = $sceneDB->InsertScene($data);

        if ($status) {
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
        break;
    case 'publish':
        $data['state'] = 0;
        $data["ctime"] = date("Y-m-d H:i:s");
        $data['uuid'] = $_SESSION['uuid'];
        $status = $sceneDB->InsertScene($data);
        if ($status) {
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
        break;
    case 'update-save':
        $data['state'] = 1;
        $data["update"] = date("Y-m-d H:i:s");
        $data['uuid'] = $_SESSION['uuid'];
        $id = $data['id'];
        $status = $sceneDB->UpdateScene($id, $data);
        if ($status) {
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
        break;
    case 'update-publish':
        $data['state'] = 0;
        $data["update"] = date("Y-m-d H:i:s");
        $data['uuid'] = $_SESSION['uuid'];
        $id = $data['id'];
        $status = $sceneDB->UpdateScene($id, $data);
        if ($status) {
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
}
?>