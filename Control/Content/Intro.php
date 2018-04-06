<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/20
 * Time: 21:23
 */
session_start();
include_once dirname(__FILE__, 2) . "/Lib/HotelManagment.php";
include_once dirname(__FILE__, 2) . "/Lib/SceneManagment.php";
include_once dirname(__FILE__, 2) . "/Lib/VisitorManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";

date_default_timezone_set("Asia/Shanghai");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    die();
}
$type = $_GET["type"];
$id = $_GET["id"];
$VisitorData = array(
    "agent" => $_SERVER["HTTP_USER_AGENT"],
    "ip" => $_SERVER["REMOTE_ADDR"],
    "ctime" => date("Y-m-d H:i:s"),
    "type" => $type,
    "sid" => $id
);
if ($type == "scene") {
    $Scene = new SceneManagment();
    $sql = "SELECT id,title,content,'scene' as type, author,date(ctime) as time,address,lat,lng" .
        " FROM $Scene->tabName" .
        " WHERE state = 0 AND id=$id";
    $data = $Scene->ExeSQL($sql);
    $Scene->UpdateViewers($id);
} else {
    $Hotel = new HotelManagment();
    $sql = "SELECT id,title,phone,content,'hotel' as type, author,date(ctime) as time,address,lat,lng" .
        " FROM $Hotel->tabName" .
        " WHERE state = 0 AND id=$id";
    $data = $Hotel->ExeSQL($sql);
    $Hotel->UpdateViewers($id);
}
$Visitor = new VisitorManagment();
$Visitor->InsertVisitor($VisitorData);
$row = $data[0];
?>
<article data-id=<?php echo $id ?> data-type=<?php echo $row['type'] ?>>
    <h2 data-address="<?php echo $row['address'] ?>">
        <a href=<?php echo "./detail.php?id=" . $id . "&type=" . $row['type'] ?> target=_blank><?php echo $row['title'] ?></a>
    </h2>
    <div class='meta clearfix' data-location="<?php echo $row['lat'] . ';' . $row['lng'] ?>">
        <span id='author'>作者: <?php echo $row['author'] ?></span>
        <span id='posttime'>发布于： <?php echo $row['time'] ?></span>
        <div class="icon-wrapper">
            <i class="icon weixin"></i>
            <i class="icon heart chearup"></i>
        </div>
    </div>
    <div class="divider ui"></div>
    <div class='viewer'>
        <div id='quill'></div>
    </div>
    <?php if ($type == 'hotel') {
        echo "<div class='phone'>" . $row['phone'] . "</div>";
    } ?>
    <style type="text/css">
        .ql-editor {
            padding: 0 !important;
        }

        .ql-editor img {
            width: 100%;
        }

        .ql-editor * {
            color: white !important;
        }
    </style>
    <script type="application/javascript">
        $(function () {
            var quillViewer = new Component.QuillViewer("#quill");
            var str = <?php echo $row['content'] ?>;
            quillViewer.view(str);
            window.quillViewer = quillViewer;
            setTimeout(function () {
                var src = $("#quill img")[0].src;
                $("meta[name='thumnail']").attr("content", src);
                $("meta[name='thumnail']").attr("content", src);
                $("meta[name='url']").attr("content", location.href);
                $("meta[name='description']").attr("content", quillViewer.getRawText().substring(0, 100));
            }, 2000)
        })
    </script>
</article>