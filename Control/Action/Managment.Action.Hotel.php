<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/3
 * Time: 1:11
 */
include_once dirname(__FILE__, 2) . "/Lib/HotelManagment.php";

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    $respond = array(
        "status" => FALSE
    );
    echo json_encode($respond);
    return;
}
$action = $_POST['action'];
$id = $_POST['id'];

try {
    $Hotel = new HotelManagment();
} catch (Exception $ex) {
    $respond = array(
        "status" => FALSE
    );
    echo json_encode($respond);
    return;
}

switch ($action) {
    case 'pull':
        $bool = $Hotel->ToDraft($id);
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        break;
    case 'waste':
        $bool = $Hotel->ToWaste($id);
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        break;
    case 'delete':
        $bool = $Hotel->Delete($id);
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        break;
    case 'restore':
        $bool = $Hotel->Restore($id);
        $respond = array(
            "status" => $bool
        );
        echo json_encode($respond);
        break;
    default:
        break;
}