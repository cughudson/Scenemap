<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/10
 * Time: 12:28
 */
session_start();
if (!$_SESSION["config"]) {
    echo "<script type='application/javascript'>alert('您没有权限访问该页面')</script>";
    die();
}
$htmlTitle = "使用前系统配置";
include_once dirname(__FILE__, 1) . "/Views/Partial/Header.php";
include_once dirname(__FILE__, 1) . "/Control/Lib/ConfigManagment.php";
$configFile = dirname(__FILE__, 1) . "/Config.ini";

$config = new ConfigManagment($configFile);
$configData = $config->Parse();

$database = array_key_exists("database", $configData) ? $configData["database"] : "";
$port = array_key_exists("port", $configData) ? $configData["port"] : "";
$host = array_key_exists("host", $configData) ? $configData["host"] : "";
$user = array_key_exists("user", $configData) ? $configData["user"] : "";
$psd = array_key_exists("psd", $configData) ? $configData["psd"] : "";
$dbname = array_key_exists("dbname", $configData) ? $configData["dbname"] : "";
$city = array_key_exists("city", $configData) ? $configData["city"] : "";
$url = array_key_exists("url", $configData) ? $configData["url"] : "";


?>
<style type="text/css">
    html, body {
        width: 100%;
        height: auto;
    }

    body {
        padding: 64px 0;
    }

    .segment {
        width: 80%;
        max-width: 460px;
        margin-left: auto !important;
        margin-right: auto !important;
        margin-top: 64px;
    }

    .segment .input, .segment .dropdown {
        width: 80%;
    }

    .segment .output-wrapper {
        width: 100%;
        height: 240px;
        padding: 10px 0;
    }

    .output {
        width: 100%;
        height: 100%;
        overflow: auto;
    }

    .output span.error, .output span.right {
        display: block;
        font-size: 12px;
    }

    .output span.error {
        color: red;
    }

    .output span.right {
        color: lightgreen;
    }
</style>
<script type="text/javascript">
    $(function () {
        $(".dropdown").dropdown();

    })
</script>
<body>
<div class="segment ui inverted">
    <h5>数据库信息配置</h5>
    <div class='ui divider'></div>
    <div class="ui selection dropdown">
        <input type="hidden" name="mysql" id='database' value="<?php echo $database ?>">
        <i class="dropdown icon"></i>
        <div class="text"><?php echo $database ?></div>
        <div class="menu">
            <div class="item" data-value="mysql">mysql</div>
            <div class="item" data-value="oracle">oracle</div>
            <div class="item" data-value="mssql">mssql</div>
        </div>
    </div>
    <div class='ui divider'></div>
    <div class='ui input'>
        <input placeholder="输入数据库地址" id='host' data-type='host' required value="<?php echo $host ?>">
    </div>
    <div class='ui divider'></div>
    <div class="ui input">
        <input placeholder="端口" id='port' data-type='port' required value= <?php echo $port ?>>
    </div>
    <div class='ui divider'></div>
    <div class="ui input">
        <input placeholder="数据库名称" id='dbname' data-type='dbname' required value= <?php echo $dbname ?>>
    </div>
    <div class='ui divider'></div>
    <div class='ui input'>
        <input placeholder="登陆名称" id='user' data-type='user' required value="<?php echo $user ?>">
    </div>
    <div class="ui divider"></div>
    <div class='ui input'>
        <input placeholder="登陆密码" id='psd' data-type='psd' required value="<?php echo $psd ?>">
    </div>
    <div class="ui divider"></div>
    <div class='ui input'>
        <input placeholder="城市" id='city' data-type='city' required value="<?php echo $city ?>">
    </div>
    <div class="ui divider"></div>
    <div class='ui input'>
        <input placeholder="网址" id='url' data-type='url' required value="<?php echo $url ?>">
    </div>
    <div class='ui divider'></div>
    <button class='ui button primary' id='test'>连接测试</button>
    <button class='ui button primary' id='save'>保存数据</button>
</div>
<div class='segment ui inverted'>
    <h5>创建数据库</h5>
    <div class='ui divider'></div>
    <button class='ui button primary' id='db'>创建数据库</button>
    <div class="output-wrapper">
        <div class='output'>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var url = "<?php echo BaseUrl . "/Control/Action/Action.DB.php"?>";
        $("#save").click(function () {

            var postData = {};
            postData['database'] = $("#database").val();
            postData['host'] = $("#host").val();
            postData['port'] = $("#port").val();
            postData['user'] = $("#user").val();
            postData['psd'] = $("#psd").val();
            postData['dbname'] = $("#dbname").val();
            postData['city'] = $("#city").val();
            postData['url'] = $("#url").val();

            $.post(url, {action: "save", data: postData}, function (data) {
                var jsonData = JSON.parse(data);
                if (jsonData.status != false) {
                    $("#db").removeClass("disabled");
                    alert("保存成功");
                } else {
                    alert("保存失败");
                }

            }).fail(function (data) {
                console.log(data);
                alert("保存失败");
            })
        });
        $("#test").click(function () {

            var postData = {};
            postData['database'] = $("#database").val();
            postData['host'] = $("#host").val();
            postData['port'] = $("#port").val();
            postData['user'] = $("#user").val();
            postData['psd'] = $("#psd").val();
            postData['dbname'] = $("#dbname").val();

            $.post(url, {action: "test", data: postData}, function (data) {

                var jsonData = JSON.parse(data);
                if (jsonData.status != false) {
                    alert("连接成功");
                } else {
                    alert("连接失败");
                }

            }).fail(function (data) {
                alert("连接失败");
            });
        });
        $("#db").click(function () {

            var that = $(this);
            var Index = 0;
            if (that.hasClass("disabled")) {
                return;
            }

            that.addClass("disabled");
            var outPut = $(".output");
            if (outPut.children().length > 0) outPut.children().remove();
            var db = ['log', 'user', 'visitor', 'scene', 'hotel'];

            var InterId = null;
            var ConstructDB = function (dbEle) {
                $.post(url, {"action": 'create', "tablename": dbEle}, function (data) {

                    console.log(data);
                    var json = JSON.parse(data);
                    if (json.status == false) {
                        outPut.append("<span class='error'>" + dbEle + "创建失败</span>");
                    } else {
                        outPut.append("<span class='right'>" + dbEle + "创建成功</span>");
                    }
                }).fail(function (data) {
                    outPut.append("<span class='error'>" + dbEle + "创建失败</span>");
                    console.error(data);
                })
            };
            var InterId = setInterval(function () {
                if (Index == db.length) {
                    clearInterval(InterId);
                    that.removeClass('disabled');
                    outPut.append("<span class='error'>运行完成</span>");
                    return;
                }
                ConstructDB(db[Index]);
                Index++;
            }, 2000)

        });

    })
</script>
</body>
<html>
