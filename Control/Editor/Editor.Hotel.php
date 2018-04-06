<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/4
 * Time: 0:49
 */
session_start();
include_once dirname(__FILE__, 2) . "/Lib/HotelManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";

date_default_timezone_set("Asia/Shanghai");
$Req_Type = $_SERVER['REQUEST_METHOD'];

if ($Req_Type == 'GET') {
    $respond = array(
        'status' => false,
    );
    echo json_encode($respond);
    die();
}
$data = $_POST['data'];

try {
    $HotelDB = new HotelManagment();
} catch (Exception $ex) {
    $respond = array(
        'status' => false,
    );
    echo json_encode($respond);
    die();
}
$action = $_POST['action'];
switch ($action) {
    case 'save':
        $data['state'] = 1;
        $data['ctime'] = date("Y-m-d H:i:s");
        $status = $HotelDB->InsertHotel($data);
        if ($tatus) {
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
        $status = $HotelDB->InsertHotel($data);
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
}
?>