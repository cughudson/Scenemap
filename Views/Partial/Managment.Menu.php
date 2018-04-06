<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/2
 * Time: 23:46
 */
session_start();
if (($_SERVER['REQUEST_METHOD']) == 'POST') return;
//超级管理员
//普通管理员的权限是不同的
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
$admin = (array_key_exists('admin', $_SESSION) && $_SESSION['admin'] == TRUE);

if (array_key_exists("auth", $_SESSION)) {

    $auth = $_SESSION['auth'];
    $sceneControl = ((string)$auth['scene']) == "true" ? true : false;
    $hotelControl = ((string)$auth['hotel']) == "true" ? true : false;
    $stateControl = ((string)$auth['state']) == "true" ? true : false;
}

?>
<body>
<div class="managment toolbar">
    <?php if ($admin || $sceneControl) { ?>
        <div class="hu-ui menu-item" data-type="newscene">
            <span class="icon">
                <i class="icon leaf"></i>
            </span>
            <span class="data">新建景点</span>
        </div>
    <?php } ?>
    <?php if ($admin || $hotelControl) { ?>
        <!--        <div class="hu-ui menu-item" data-type="newhotel">-->
        <!--            <span class="icon">-->
        <!--                <i class="ui icon cube"></i>-->
        <!--            </span>-->
        <!--            <span class="data">新建民宿</span>-->
        <!--        </div>-->
    <?php } ?>
    <?php if ($admin || $sceneControl) { ?>
        <div class="hu-ui menu-item" data-type="scenemanagment">
            <span class="icon">
                <i class="icon world"></i>
            </span>
            <span class="data">景点管理</span>
        </div>
    <?php } ?>
    <?php if ($admin || $hotelControl) { ?>
        <!--        <div class="hu-ui menu-item" data-type="hotelmanagment">-->
        <!--            <span class="icon">-->
        <!--                <i class="icon cubes"></i>-->
        <!--            </span>-->
        <!--            <span class="data">民宿管理</span>-->
        <!--        </div>-->
    <?php } ?>
    <?php if ($admin || $stateControl) { ?>
        <div class="hu-ui menu-item" data-type="state">
            <span class="icon">
                <i class="icon connectdevelop"></i>
            </span>
            <span class="data">运行状态</span>
        </div>
    <?php } ?>
    <div class="hu-ui menu-item" data-type="sysmanagment">
            <span class="icon">
                <i class="icon settings"></i>
            </span>
        <span class="data">系统管理</span>
    </div>
</div>