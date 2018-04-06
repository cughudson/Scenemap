<?php

include_once dirname(__FILE__, 2) . "/Lib/SceneManagment.php";
include_once dirname(__FILE__, 2) . "/Lib/ConfigManagment.php";
//include_once dirname(__FILE__,3)."/Helper/ChromePhp.php";
header("cache-control:no-cache");
if ($_SERVER["REQUEST_METHOD"] == "POST") die();
$Scene = new SceneManagment();
if (key_exists("bounds", $_GET)) {
    $bound = $_GET["bounds"];
    $SceneSQL = "SELECT id,lat,lng,author,title,surface,date(ctime) as ctime,'scene' as type FROM $Scene->tabName" .
        " WHERE lat >" . (double)$bound['swt'] . " and lat<" . (double)$bound['net'] . " and lng>" . (double)$bound['swg'] . " and lng<" . (double)$bound['neg'] .
        " AND state = 0" .
        " LIMIT 50";
} else {
    $id = $_GET["id"];
    $SceneSQL = "SELECT id,lat,lng,author,title,surface,date(ctime) as ctime, 'scene' as type FROM $Scene->tabName" .
        " WHERE id=$id AND state=0";
}
$sceneData = $Scene->ExeSQL($SceneSQL);
$respond = array(
    "data" => $sceneData,
    "status" => true,
);
echo json_encode($respond);
?>