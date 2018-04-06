<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/4
 * Time: 1:06
 */
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


include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
include_once dirname(__FILE__, 3) . "/Control/Lib/SceneManagment.php";
$Scene = new SceneManagment();
$BaseUrl = $Scene->configData["url"];
$count = $Scene->GetTotalCountByState(0);
$maxPage = ceil($count / $Scene->configData['rowPerPage']);
?>
<section id='tab-group'>
    <div class="table-header">
        <div class="ui top attached tabular menu">
            <a class="item" data-state=0 id="publish">已发布</a>
            <a class="item" data-state=1 id="draft">草稿箱</a>
            <a class="item" data-state=2 id="recycle">回收箱</a>
            <div class='button-group'>
                <div class='reflesh button' id='reflesh'>
                    <i class='icon refresh'></i>
                </div>
            </div>
        </div>
    </div>
    <div class='table-wrapper'>
        <table class="ui celled table">
            <thead>
            <tr>
                <th>序列</th>
                <th>标题</th>
                <th>作者</th>
                <th>创建时间</th>
                <th>访问者</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>
    <div class="ui message positive">
        <div class='header'>注意事项</div>
        <p>单击删除按钮并不会将数据从数据库中直接删除，而是会将数据保存到回收箱中，如果需要将数据从数据库中删除，请到清空回收箱或在回收箱中删除数据</p>
        <i class='ui icon close'></i>
    </div>
</section>
<script type="application/javascript">
    $(function () {
        $(".tabular .item").unbind("click").bind("click", function (evt) {
            var url = "<?php echo $BaseUrl . "/Control/Tab/SceneFooter.php"?>";
            var that = $(this);
            var state = that.data('state');
            if (that.hasClass('active')) return;
            $(".tabular .item").removeClass('active');
            that.addClass('active');

            $("table").data('state', state);
            $.get(url, {"state": state}, function (html) {

                if ($("table tfoot").children().length > 0) $("table tfoot").children().remove();
                $("table tfoot").append(html);
                var currentState = parseInt(state);
                FetchData(1, currentState);
            }).fail(function () {
                console.error("加载失败");
            })
        });
        $(".tabular .item").first().click();
        var FetchData = function (page, state) {
            var url = ' <?php echo $BaseUrl . "/Control/Data/Managment.Data.Scene.php"?>';
            $.get(url, {page: page, state: state}, function (respondData) {
                $("#loader").remove();
                if ($("table tbody").children().length > 0) $("table tbody").children().remove();
                $("table tbody").append(respondData);
            });
        };
    })
</script>
</div>
