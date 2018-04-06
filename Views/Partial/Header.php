<?php
header("Content-Type:text/html; charset=utf8");
include_once dirname(__FILE__, 3) . "/Control/Lib/ConfigManagment.php";
$fileDir = dirname(__FILE__, 3) . "/Config.ini";
$ConfigData = (new ConfigManagment($fileDir))->Parse();
define('BaseUrl', $ConfigData['url']);
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $htmlTitle ?></title>
    <meta http-equiv="content-type" content="text/html;charset=utf8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="viewport" content="width=device-width,  initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="thumnail" property="og:image" content="">
    <meta name="type" property="og:type" content="website"/>
    <meta name="description" property="og:description" content="">
    <meta name="url" property="og:url" content="">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel='stylesheet' type='text/css' href=<?php echo BaseUrl . "/Assert/css/semantic.css" ?>/>
    <link rel="stylesheet" type="text/css" href=<?php echo BaseUrl . "/Assert/css/component.css" ?>/>
    <link rel="stylesheet" type="text/css" href=<?php echo BaseUrl . "/Assert/css/page.css" ?>/>
    <link rel="stylesheet" type="text/css" href=<?php echo BaseUrl . "/Assert/css/quilljs.snow.editor.css" ?>/>
    <link rel="apple-touch-icon" href=<?php echo BaseUrl . "/Assert/icon/ico.png" ?>/>
    <link rel="icon" href=<?php echo BaseUrl . "/Assert/icon/ico.png" ?>/>
    <script type="text/javascript"
            src="http://api.map.baidu.com/api?v=3.0&ak=cUWciM1b3cacMFaSKRgTGT9W5yfh7RF7"></script>
    <script type="text/javascript" src=<?php echo BaseUrl . "/Assert/js/plugin/quill/quill.js" ?>></script>
    <script type="text/javascript" src=<?php echo BaseUrl . "/Assert/js/jquery-3.3.1.js" ?>></script>
    <script type="text/javascript" src=<?php echo BaseUrl . "/Assert/js/jquery-ui.min.js" ?>></script>
    <script type='text/javascript' src=<?php echo BaseUrl . "/Assert/js/semantic.js" ?>></script>
    <script type="text/javascript" src=<?php echo BaseUrl . "/Assert/js/component.js" ?>></script>
</head>
