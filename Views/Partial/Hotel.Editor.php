<?php
session_start();
header("Content-Type:text/html; charset=utf8");
$BaseUrl = "http://localhost:63342/HomeTown";
include_once dirname(__FILE__, 3) . "/Control/Lib/ConfigManagment.php";
$fileDir = dirname(__FILE__, 3) . "/Config.ini";

$Config = new ConfigManagment($fileDir);
$city = $Config->Parse()['city'];
?>
<section class="editor" id="editor" data-userid= <?php echo $_SESSION['userid'] ?>>
    <div class='ui input'>
        <input id="title" type="text" autocomplete=false placeholder="输入标题" for="editor" required autofocus
               maxlength=20>
    </div>
    <div class='ui divider'></div>
    <div class='ui input'>
        <input id="author" type="text" autocomplete=true placeholder="填写作者" maxlength=10 required
               value="<?php echo $_SESSION['name'] ?>">
    </div>
    <div class='ui divider'></div>
    <div class='ui input'>
        <input id="phoneNum" type="text" autocomplete=true placeholder="电话号码" maxlength=11 required val=>
    </div>
    <div class='ui divider'></div>
    <div class="input-group">
        <button id='geo-picker-btn' class="ui primary button" title="打开地图拾取坐标">拾取坐标</button>
        <input id='geolocation' type='hidden'>
        <input id='address' type='hidden'>
        <div class="geo-data label" id='geo-data'>
        </div>
        <div class="ui message info" id='markerPos'>
            <p>这里将显示拾取坐标的点的位置</p>
        </div>
    </div>
    <div class='ui divider'></div>
    <div class="article-content hidden-data"></div>
    <div class="rich-editor-wrapper">
        <h5>旅馆描述(添加的图片每张不要超过200K)</h5>
        <div id="rich-editor"></div>
        <div class="drag-bar">
            <span>
                <i class="angle double down icon"></i>
            </span>
        </div>
    </div>
    <div class="row">
        <button id="save" class='ui primary button'>保存</button>
        <button id="publish" class='ui primary button'>发布</button>
    </div>
</section>
</div>
<div id="geo-picker-pannel" class="panel">
    <div class="close-wrapper clearfix">
        <span class="close clickable">
            <i class="icon close"></i>
        </span>
    </div>
    <div class='map-wrapper'>
        <div id="map"></div>
        <div id="maxisize">
            <i class='icon maximize'></i>
        </div>
    </div>
    <script>
        $(function () {
            $('#maxisize').click(function () {
                var map = $("#map")[0];
                if (map.webkitRequestFullscreen) {
                    map.webkitRequestFullscreen();
                }
                if (map.mozRequestFullscreen) {
                    map.mozRequestFullscreen();
                } else {
                    // map.RequestFullscreen();
                }
            })
        })
    </script>
    <div class="ui message positive">
        <p>双击地图标定所需要选取的坐标点，确认，保存拾取数据，取消，放弃拾取</p>
    </div>
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
            //console.log("close");
        });
        $("#geo-picker-btn").click(
            function () {
                rightPanel.Open(function () {
                    console.log("open");
                });
            }
        );
        rightPanel.Cancel(function () {
            //console.log("cancel");
        });
        rightPanel.OK(function (ele) {
            $('#geo-data').children().remove();
            $("#geo-data").append('<a class="ui teal tag label">' + "<span>" + map.value2() + "</span>" + "<i class='delete icon'></i></a>");
            $("#markerPos p").text(map.addressText);
            console.log(map);
            $("input#geolocation").val(map.value());
            $("input#address").val(map.address);
        });
        var editor = new Component.quillEditor("#rich-editor");

        $(document).on('click', '.tag .delete', function () {
            $(this).parents('.tag').remove();
            $("#markerPos p").text("这里将显示拾取坐标点的位置");
            $("#address").val("");
            $("#geolocation").val("");
        });
        //保存文章信息
        $("#save").click(function () {

            var saveBtn = $("#save");
            var publishBtn = $("#publish");

            if (saveBtn.hasClass("disabled")) return;

            var postData = {};
            var saveData = {};
            var action = 'save';
            var url = '<?php echo $BaseUrl . "/Control/Editor/Editor.Hotel.php"?>';
            saveData.title = $('#title').val();
            saveData.author = $('#author').val();
            saveData.phoneNum = $("#phoneNum").val();
            saveData.lng = JSON.parse($('input#geolocation').val()).lng;
            saveData.lat = JSON.parse($('input#geolocation').val()).lat;
            saveData.address = $("input#address").val();
            saveData.content = JSON.stringify(editor.getContents());
            saveData.tauthor = $("section#editor").data('userid');
            if (publishData.title == "" || publishData.phoneNum == "") {

            }
            postData.action = "save";
            postData.data = saveData;
            $.post(url, postData, function (respond) {
                var json = JSON.parse(respond);
                if (json.status) {
                    alert("保存成功");
                    saveBtn.addClass("disabled");
                    publishBtn.addClass("disabled");
                } else {
                    alert("保存失败");
                }
            });
        });
        //发布文章信息
        $("#publish").click(function () {

            var saveBtn = $("#save");
            var publishBtn = $("#publish");

            if (publishBtn.hasClass("disabled")) return;

            var postData = {};
            var publishData = {};
            var action = 'save';
            var url = '<?php echo $BaseUrl . "/Control/Editor/Editor.Hotel.php"?>';

            publishData.title = $('#title').val();
            publishData.author = $('#author').val();
            publishData.phoneNum = $("#phoneNum").val();
            publishData.lng = JSON.parse($('input#geolocation').val()).lng;
            publishData.lat = JSON.parse($('input#geolocation').val()).lat;
            publishData.address = $("input#address").val();
            publishData.content = JSON.stringify(editor.getContents());
            publishData.tauthor = $("section#editor").data("userid");

            postData.data = publishData;
            postData.action = "publish";

            $.post(url, postData, function (respond) {
                var json = JSON.parse(respond);
                if (json.status) {
                    alert("发布成功");
                    saveBtn.addClass("disabled");
                    publishBtn.addClass("disabled");
                } else {
                    alert("发布失败");
                }
            });
        });
    })
</script>