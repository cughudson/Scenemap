<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/3
 * Time: 1:12
 */
session_start();
header("Content-Type:text/html;charset=utf-8");
include_once dirname(__FILE__, 2) . "/Lib/ConfigManagment.php";
$filePath = dirname(__FILE__, 3) . "/Config.ini";
$configData = (new ConfigManagment($filePath))->Parse();
$url = $configData["url"];
session_unset();
session_destroy();
header('location:' . $url . "/login.php", 301);