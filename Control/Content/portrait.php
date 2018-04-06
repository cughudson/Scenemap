<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/23
 * Time: 9:53
 */
session_start();
Header("Content-Type:image/jpeg");

$name = $_SESSION["name"];
$height = 100;
$width = 100;
$fontSize = 40;
$margin = 70;

$font = dirname(__FILE__, 3) . "/Assert/fonts/msyh.ttc";
$text = mb_substr($name, 0, 1, "UTF-8");
//  echo $font;
//
$portrait = imagecreatetruecolor($width, $height);

$blue = imagecolorallocate($portrait, 3, 169, 244);
$white = imagecolorallocate($portrait, 255, 255, 255);

imagefill($portrait, 0, 0, $blue);
imagettftext($portrait, $fontSize, 0, $margin - 47, $margin, $white, $font, $text);
imagejpeg($portrait);
imagedestroy($portrait);
?>

