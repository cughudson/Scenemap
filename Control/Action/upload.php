<?php
/**
 * Created by PhpStorm.
 * User: ZJ3
 * Date: 2018-02-26
 * Time: 16:14 PM
 */

session_start();
if (!array_key_exists("login", $_SESSION)) {
    die();
}
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    die();
}
$file = $_FILES['file'];
if ($file == "") {
    die();
}
$id = uniqid("", true);
$imageName = "jpeg" . $id . ".jpg";

$targetImage = dirname(__FILE__, 3) . "/Assert/Resource/image/" . $imageName;


$image = imagescale($file, 256, 144);

move_uploaded_file($file['tmp_name'], $targetImage);

$respond = array(
    "fileName" => $imageName,
    "fullPath" => "./Assert/Resource/image/" . $imageName
);
echo json_encode($respond);
?>