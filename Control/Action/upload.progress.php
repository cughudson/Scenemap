<?php
/**
 * Created by PhpStorm.
 * User: ZJ3
 * Date: 2018-02-26
 * Time: 17:28 PM
 */
session_start();
if ($_SERVER['REQUEST_METHOD'] == "POST") die();
$item = ini_get('session.upload_progress.name');
$key = ini_get('session.upload_progress.prefix');

if (!empty($_SESSION[$key])) {
    $current = $_SESSION[$key]["bytes_processed"];
    $total = $_SESSION["$key"]["content_length"];
    echo $current < $total ? ceil($current / $total * 100) : 100;
} else {
    echo 100;
}