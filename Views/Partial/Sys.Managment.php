<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/4
 * Time: 13:16
 */
session_start(['cookie_lifetime' => 30 * 60 * 24]);
if (!array_key_exists("login", $_SESSION)) {
    echo "<script type='text/javascript'>alert('检测到您尚未登陆系统，请登陆')</script>";
    echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
    die();
}
if (isset($_SESSION['time'])) {
    if ($_SESSION['time'] < time()) {
        session_unset();
        session_destroy();
        echo "<script type='text/javascript'>alert('会话已经过期，如需继续操作，请重新登陆')</script>";
        echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
        die();
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}
include_once dirname(__FILE__, 3) . "/Control/Lib/ConfigManagment.php";
$fileDir = dirname(__FILE__, 3) . "/Config.ini";
$ConfigData = (new ConfigManagment($fileDir))->Parse();
$BaseUrl = $ConfigData["url"];
?>
<section id='tab-group'>
    <div class="table-header">
        <div class="ui top attached tabular menu">
            <a class="item" data-type="user" id="user-mang">用户管理</a>
            <a class='item' data-type='sys-config'>系统配置</a>
        </div>
    </div>
    <script type="application/javascript">
        $(function () {
            var base = "<?php echo $BaseUrl . "/Views/Partial/"?>";
            $(".tabular .item").click(function (evt) {
                var that = $(this);
                var state = that.data('state');
                if (that.hasClass('active')) return;
                //toggle
                $(".tabular .item").removeClass('active');
                that.addClass('active');
                if (that.data('type') == 'user') {
                    url = base + "Sys.Managment.User.php";
                } else {
                    url = base + "Sys.Managment.Config.php";
                }
                $.post(url, {}, function (respond) {
                    if ($(".table-wrapper").children().length > 0) $(".table-wrapper").children().remove();
                    $(".table-wrapper").append(respond);

                }).fail(function (data) {
                    alert("加载失败");
                    $("table-wrapper").append("<div class='ui info message error'>加载失败</div>");
                });
            });
            $("#user-mang").click();
        })
    </script>
    <div class='content-wrapper'>
        <div class='table-wrapper'>
        </div>
    </div>
</section>
