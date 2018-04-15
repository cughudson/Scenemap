<?php
session_start(['cookie_lifetime' => 30 * 60 * 24]);
header("Content-Type:text/html; charset=utf8");

if (!array_key_exists("login", $_SESSION)) {
    echo "<script type='text/javascript'>alert('检测到您尚未登陆系统，请登陆')</script>";
    echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
    die();
}
if (isset($_SESSION['time'])) {
    if ($_SESSION['time'] < time()) {
        echo "<script type='text/javascript'>alert('会话已经过期，如需继续操作，请重新登陆')</script>";
        echo "<script type='text/javascript'>window.top.location.href ='./../../login.php'</script>";
        die();
    } else {
        $_SESSION['time'] = time() + 30 * 60;
    }
}

include_once dirname(__FILE__, 3) . "/Control/Lib/ConfigManagment.php";
$fileDir = dirname(__FILE__, 3) . "/Config.ini";
$isAdmin = array_key_exists("admin", $_SESSION) && $_SESSION["admin"];
$ConfigData = (new ConfigManagment($fileDir))->Parse();
$BaseUrl = $ConfigData["url"];
?>
<section class='row' data-userid= <?php echo $_SESSION['uuid'] ?>>
    <button class="circular ui icon button blue" id="add-editor" title="新建景点">
        <i class="icon add"></i>
    </button>
</section>
<hr/>
<br/>
<section id="editor" class="editor">
</section>
<script type="application/javascript">
    $(function () {
        $(document).on("click", "#add-editor", function () {
            var url = "<?php echo $BaseUrl . "/Views/Partial/Scene.Editor.php";?>";
            $.get(url, function (respond) {
                if ($("#editor").children().length > 0) $("#editor").children().remove();
                $("#editor").append(respond);
            }).fail(function () {
                alert("加载失败");
            })
        })
    })
</script>