<?php
session_start(['cookie_lifetime' => 30 * 60 * 24]);
header("Content-Type:text/html; charset=utf8");

if (!array_key_exists("login", $_SESSION)) {
    echo "<script type='text/javascript'>alert('检测到您尚未登陆系统，请登陆')</script>";
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

include_once dirname(__FILE__, 3) . "/Control/Lib/SceneManagment.php";
include_once dirname(__FILE__, 3) . "/Helper/ChromePhp.php";
$Scene = new SceneManagment();
$id = $_GET['id'];
$userId = $_SESSION['uuid'];
$authorId = $Scene->GetSceneAuthorId($id);
if ($isAdmin) {
    $city = $ConfigData["city"];
} else {
    if ($_COOKIE["city"]) {
        $city = $_COOKIE['city'];
    } else {
        $city = $ConfigData['city'];
    }
}
$BaseUrl = $Scene->configData["url"];
if ($userId != $authorId && $userId != "admin") {
    $data = array();
    die("<script type='text/javascript'>alert('无权限访问该文章')</script>");
} else {
    $sql = "SELECT id,title,content,authorid,address,lat,lng" .
        " FROM $Scene->tabName" .
        " WHERE id=$id";
    $row = $Scene->ExeSQL($sql);
    $data = $row[0];
}
?>
<main class="managment-wrapper" data-userid="<?php echo $_SESSION["userid"] ?>">
    <header class='clearfix'>
        <div class="hu-ui user">
            <img class="portrait" src=<?php echo BaseUrl . "/Control/Content/portrait.php" ?>>
            <a class="header" href='javascript:void(0)'><?php echo $_SESSION["name"] ?></a>
            <a class='logout' href=<?php echo BaseUrl . "/Control/Action/Action.logout.php" ?>>退出</a>
        </div>
    </header>
    <div id='managment' class="managment">
        <?php
        if (count($data) == 0) {
            echo "<div class='ui warning message'>检索的内容不存在</div>";
            echo "</main>
                  </body>
                  </html>";
            die();
        }
        ?>
        <section id="editor" class="editor" data-userid="<?php echo $_SESSION['uuid'] ?>"
                 data-id=<?php echo $data['id'] ?>>
            <div class='ui input'>
                <input id="title" type="text" autocomplete=false placeholder="输入标题" for="editor" required autofocus
                       maxlength=20 value="<?php echo $data['title'] ?>">
            </div>
            <div class='ui divider'></div>
            <div class='ui input'>
                <input id="author" type="text" autocomplete=true placeholder="填写作者" maxlength=10 required
                       value="<?php echo $_SESSION['name'] ?>">
            </div>
            <div class='ui divider'></div>
            <div class="input-group">
                <button id='geo-picker-btn' class="ui primary button" title="打开地图拾取坐标">拾取坐标</button>
                <input id='geolocation' type='hidden'
                       value=<?php echo '{"lng":' . $data['lng'] . ',' . '"lat":' . $data['lat'] . '}' ?>>
                <input id='address' type='hidden' value='<?php echo $data['address'] ?>'>
                <div class="geo-data label" id='geo-data'>
                    <a class="ui teal tag label" id="geoinfo">
                        <span><?php echo $data['lng'] . ',' . $data['lat'] ?></span><i class="delete icon"></i>
                    </a>
                </div>
                <div class="ui message info" id='markerPos'>
                    <p>
                        <?php
                        $address = json_decode($data['address'], true);
                        $reversedArr = array_reverse($address);
                        $str = implode(',', $reversedArr);
                        echo $str;
                        ?>
                    </p>
                </div>
            </div>
            <div class='ui divider'></div>
            <div class="article-content hidden-data"></div>
            <div class="rich-editor-wrapper">
                <h5>景点描述(添加的图片每张不要超过256KB至少上传一张图片)</h5>
                <div id="rich-editor">
                </div>
                <div class="drag-bar">
                    <span><i class="angle double down icon"></i></span>
                </div>
            </div>
            <div class="row">
                <button id="publish" class='ui primary button'>发布</button>
                <button id="save" class='ui primary button hasclick'>保存</button>
            </div>
        </section>
        <div id="geo-picker-pannel" class="panel">
            <div class="close-wrapper clearfix">
                <span class="close clickable"><i class="icon close"></i></span>
            </div>
            <div class='map-wrapper'>
                <div id="map"></div>
                <div id="maxisize">
                    <i class='icon maximize'></i>
                </div>
            </div>
            <script type="application/javascript">
                $(function () {
                    $('#maxisize').unbind("click").bind("click", function () {
                        var mapEle = $("#map")[0];
                        //这个地方存在一个问题不能够触发事件
                        window.map.map.panTo(window.map.picker());
                        if (mapEle.webkitRequestFullscreen) {
                            mapEle.webkitRequestFullscreen();
                        }
                        if (mapEle.mozRequestFullscreen) {
                            mapEle.mozRequestFullscreen();
                        } else {
                            //map.RequestFullscreen();
                        }
                        window.map.map.panTo(window.map.picker());
                    });
                    //esc delete enter shift 等键需要keydown触发，keypress不可以
                    $(document).unbind("keydown").bind("keydown", function (evt) {
                        //debugger;
                        if (evt.which == 27) {
                            window.map.map.panTo(window.map.picker());
                        }
                    })
                })
            </script>
            <div class="ui message positive"><p>双击地图标定所需要选取的坐标点，确认，保存拾取数据，取消，放弃拾取</p></div>
            <div class="toolbar clearfix">
                <div class="ui primary button ok">确认</div>
                <div class="ui primary button cancel">取消</div>
            </div>
        </div>
        <script type="text/javascript">
            $(document).ready(function () {
                var city = "<?php echo $city?>";
                var map = (new Component.PickerMap("map", city, 11, "baidu")).init();
                map.enableDbClick();

                window.rightPanel = new Component.RightPanel("#geo-picker-pannel");

                rightPanel.Close(function () {
                    //TODO
                });
                $("#geo-picker-btn").click(
                    function () {
                        rightPanel.Open(function () {
                            //TODO
                        });
                    }
                )
                rightPanel.Cancel(function () {
                });
                rightPanel.OK(function (ele) {
                    $('#geo-data').children().remove();
                    $("#geo-data").append('<a class="ui teal tag label" id="geoinfo">' + "<span>" + map.value2() + "</span>" + "<i class='delete icon'></i></a>");
                    $("#markerPos p").text(map.addressText);
                    $("input#geolocation").val(map.value());
                    $("input#address").val(map.address);
                });
                var uploadUrl = "<?php echo $BaseUrl . "/Control/Action/upload.php"?>";
                window.editor = new Component.quillEditor("#rich-editor");
                var str = <?php echo $data['content']?>;
                editor.setContents(str);
                editor.AddImageHandler(uploadUrl);
                $(document).on('click', '.tag .delete', function () {

                    var parentTag = $(this).parents('.tag');
                    if (parentTag.hasClass("disabled")) return;
                    parentTag.remove();
                    $("#markerPos p").text("这里将显示拾取坐标点的位置");
                    $("#address").val("");
                    $("#geolocation").val("");
                });

                //保存文章信息
                $("#save").click(function () {
                    var that = $(this);
                    if (that.hasClass('disabled')) return;
                    var url = '<?php echo $BaseUrl . "/Control/Editor/Editor.Scene.php" ?>';
                    var postData = {};
                    var data = {};
                    if ($('#title').val() == "" || $('#author').val() == "" || $('input#geolocation').val() == "") {
                        alert("有部分内容未填写,请填写完后提交");
                        return;
                    }
                    var imageList = $(".ql-editor img");

                    data.id = $("#editor").data("id");
                    data.title = $('#title').val();
                    data.author = $('#author').val();
                    data.city = "南昌";
                    data.lng = JSON.parse($('input#geolocation').val()).lng;
                    data.lat = JSON.parse($('input#geolocation').val()).lat;
                    data.address = $("input#address").val();
                    data.content = JSON.stringify(editor.getContents());
                    data.city = JSON.parse(data.address)['city'] ? JSON.parse(data.address)["city"] : "unknow";
                    data.surface = imageList.length == 0 ? "" : imageList.eq(0).attr("src").split("/").pop();

                    postData.action = "update-save";
                    postData.data = data;

                    $.post(url, postData, function (respond) {
                        var json = JSON.parse(respond);
                        if (json.status) {
                            DisabledEditor();
                            alert("更新成功");
                        } else {
                            alert("更新失败");
                        }
                    }).fail(function (data) {
                        console.log("更新失败");
                    });
                });

                //发布文章信息
                $("#publish").click(function () {

                    var that = $(this);
                    if (that.hasClass('disabled')) return;
                    var url = '<?php echo $BaseUrl . "/Control/Editor/Editor.Scene.php" ?>';
                    var postData = {};
                    var data = {};
                    if ($('#title').val() == "" || $('#author').val() == "" || $('input#geolocation').val() == "") {
                        alert("有部分内容未填写,请填写完后提交");
                        return;
                    }
                    var imageList = $(".ql-editor img");
                    data.id = $("#editor").data("id");
                    data.title = $('#title').val();
                    data.author = $('#author').val();
                    data.city = "南昌";
                    data.lng = JSON.parse($('input#geolocation').val()).lng;
                    data.lat = JSON.parse($('input#geolocation').val()).lat;
                    data.address = $("input#address").val();
                    data.content = JSON.stringify(editor.getContents());
                    data.city = JSON.parse(data.address)['city'] ? JSON.parse(data.address)["city"] : "unknow";
                    data.surface = imageList.length == 0 ? "" : imageList.eq(0).attr("src").split("/").pop();
                    postData.action = "update-publish";
                    postData.data = data;

                    $.post(url, postData, function (respond) {
                        var json = JSON.parse(respond);
                        if (json.status) {

                            DisabledEditor();
                            alert("更新成功");
                        } else {
                            alert("更新失败");
                        }
                    }).fail(function () {
                        alert("更新失败");
                    });
                });
                var DisabledEditor = function () {
                    editor.quill.disable(true);
                    $("input[type='text'").attr('disabled', true);
                    $(".button").addClass("disabled");
                    $(".ui.tag").addClass("disabled");
                }
            })
        </script>
    </div>
</main>
</body>
</html>