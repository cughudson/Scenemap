<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/3
 * Time: 1:11
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
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}
include_once dirname(__FILE__, 2) . "/Lib/UserManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
date_default_timezone_set("Asia/Shanghai");

if ($_SERVER['REQUEST_METHOD'] == "GET") die();

$action = $_POST['action'];
$data = $_POST['data'];
try {
    $User = new UserManagment();
} catch (Exception $ex) {
    $respond = array(
        "status" => FALSE
    );
    echo json_encode($respond);
    die();
}

switch ($action) {
    case 'modifyname':
        $id = $data["id"];
        $user = $data["user"];
        if ($User->IsExisit("user", $user)) {
            $respond = array(
                "status" => false,
            );
            echo json_encode($respond);
            die();
        }
        $bool = $User->UpdateUserName($id, $user);
        if ($bool) {
            $temp = true;
        } else {
            $temp = false;
        }
        $respond = array(
            "status" => $temp,
        );
        echo json_encode($respond);
        break;
    case 'modifypsd':
        $id = $data["id"];
        $psd = $data["psd"];
        if (!$User->IsExisit("id", $id)) {
            $respond = array(
                "status" => false,
            );
            echo json_encode($respond);
            die();
        };
        $bool = $User->UpdateUserPsd($id, $psd);
        if ($bool) {
            $temp = true;
        } else {
            $temp = false;
        }
        $respond = array(
            "status" => $temp,
        );
        echo json_encode($respond);
        die();
        break;
    case 'delete':
        $id = $data['id'];
        $temp = $User->DeleteUser($id);
        $bool = $temp == 1 ? true : false;
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        die();
        break;
    case 'resetpsd':
        $id = $data['id'];
        $bool = $User->ResetUserPsd($id);
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        die();
        break;
    case 'add':
        $user = $data["user"];
        $IsExist = $User->IsExisit("user", $user);
        if ($IsExist) {
            $respond = array(
                "status" => false,
            );
            echo json_encode($respond);
            die();
        }
        $data['ctime'] = date("Y-m-d H:i:s");
        $data['auth'] = json_encode($data['auth']);
        $data['psd'] = "888888";
        $id = $User->InsertUser($data);
        if ($id) {
            $respond = array(
                "status" => true,
                "id" => $id,
                "ctime" => $data['ctime'],
            );
        } else {
            $respond = array(
                "status" => false,
            );
        }
        echo json_encode($respond);
        die();
    case "update":
        $id = $data['id'];
        $IsExist = $User->IsExisit("id", $id);
        if (!$IsExist) {
            $respond = array(
                "status" => false,
            );
            echo json_encode($respond);
            die();
        }
        $data['auth'] = json_encode($data['auth']);
        $temp = $User->UpdateUser($data);
        if ($temp) {
            $respond = array(
                "status" => true,
            );
        } else {
            $respond = array(
                "status" => false,
            );
        }
        echo json_encode($respond);
        die();
    default:
        break;
}