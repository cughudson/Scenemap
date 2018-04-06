<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/20
 * Time: 21:23
 */

include_once dirname(__FILE__, 1) . "/Control/Lib/HotelManagment.php";
include_once dirname(__FILE__, 1) . "/Control/Lib/SceneManagment.php";
include_once dirname(__FILE__, 1) . "/Control/Lib/VisitorManagment.php";
include_once dirname(__FILE__, 1) . "/Helper/ChromePhp.php";

date_default_timezone_set("Asia/Shanghai");
$type = $_GET["type"];
$id = $_GET["id"];
$VisitorData = array(
    "agent" => $_SERVER["HTTP_USER_AGENT"],
    "ip" => $_SERVER["SERVER_ADDR"],
    "ctime" => date("Y-m-d H:i:s"),
    "type" => $type,
    "sid" => $id,
);
if ($type == "scene") {
    $Scene = new SceneManagment();
    $sql = "SELECT id,title,content,'scene' as type, author,date(ctime) as time,address,lat,lng" .
        " FROM $Scene->tabName" .
        " WHERE state = 0 AND id=$id";
    $data = $Scene->ExeSQL($sql);
    $Scene->UpdateViewers($id);
} else {
    echo "<div class='ui info message'>无符合条件的数据</div>";
    die();
}
$Visitor = new VisitorManagment();
$Visitor->InsertVisitor($VisitorData);
$Article = $data[0];
if (array_count_values($data) == 0) {
    echo "<div class='ui info message'>无符合条件的数据</div>";
    die();
}
$htmlTitle = $Article["title"];
require_once dirname(__FILE__, 1) . "/Views/Partial/Header.php";
?>
</head>
<body id="detail" data-id="<?php echo $id ?>">
<script type="application/javascript" src=<?php echo BaseUrl . "/Assert/js/BMap.js" ?>></script>
<header id="detail-header" class="clearfix">
    <a href=<?php echo BaseUrl . "/map.php?id=" . $id . "&type=" . $Article["type"] ?> target="_self">
            <span class="" title="地图">
                <svg fill="#ffffff" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36">
                    <path d="M30 16.5H11.74l8.38-8.38L18 6 6 18l12 12 2.12-2.12-8.38-8.38H30v-3z"/>
                </svg>
            </span>
    </a>
</header>
<body>
<div class="static-map">
    <div id="map"></div>
    <div class="hide-brand"></div>
</div>
<div class="article-detail-wrapper">
    <div class="article-detail">
        <h1><?php echo $Article["title"] ?> </h1>
        <div class="meta clearfix">
            <span id='author'>作者: <?php echo $Article['author'] ?></span><span class="space"></span>
            <span id='posttime' style="padding-left: 24px">发布于： <?php echo $Article['time'] ?></span>
        </div>
        <div class="ui divider"></div>
        <div id="article">
        </div>
    </div>
    <div class="row center extra-huge chearup-row">
        <button class="circular ui icon button  hu-huge chearup" style="background: none">
            <i class="icon empty heart"></i>
        </button>
    </div>
</div>
</body>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
</body>
<style type="text/css">
    .ql-editor {
        padding: 0 !important;
    }

    .ql-editor img {
        width: 100%;
    }
</style>
<script type="text/javascript">
    $(function () {
        $(".chearup").click(function () {
            var itemId = $("body").data("id");
            var that = $(this);
            var url = "<?php echo BaseUrl . '/Control/Action/ChearUp.php'?>";
            $.post(url, {"id": itemId}, function (data) {
                    var obj = JSON.parse(data);
                    if (obj.status) {
                        $('i', that).removeClass("empty");
                    } else {
                        alert("你已经点赞过该篇文章，且半个小时之内只能点赞一次");
                    }
                }
            ).fail(function () {
                alert("点赞失败");
            })
        });
    })
</script>
<script type="application/javascript">
    $(function () {
        var quill = new Quill("#article");
        var str = <?php echo $Article['content'] ?>;
        quill.setContents(str);
        setTimeout(function () {
            var lng = <?php echo $Article['lng']?>;
            var lat = <?php echo $Article['lat']?>;
            var pt = new BMap.Point(lng, lat);
            HuMap.init("map", pt, 11);
            HuMap.DisableUserControl();
            HuMap.map.panBy(0, -70, true);
        }, 300);
        $("meta[name='description']").attr("content", $(".ql-editor").text().substring(0, 120));
        setTimeout(function () {
            var src = $("#article img")[0].src;
            $("meta[name='thumnail']").attr("content", src);
            $("meta[name='url']").attr("content", location.href);
            $("meta[name='description']").attr("content", quill.getText().substring(0, 100));
        }, 2000)
    })
</script>
<script type="application/javascript">
    $(function () {
        wx.onMenuShareAppMessage({
            title: $("h1").text(),
            desc: $("meta[name='description']").attr("content"), // 分享描述
            link: location.href,
            imgUrl: $("#article img")[0].src, // 分享图标
            type: '',
            dataUrl: location.href,
            success: function () {
                alert("success");
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                alert("failed");
                // 用户取消分享后执行的回调函数
            }
        });
    })
</script>
</html>