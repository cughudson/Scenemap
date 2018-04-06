<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/4
 * Time: 0:57
 */
header("Access-Control-Allow-Origin: *");
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
$htmlTitle = "系统管理";
include_once dirname(__FILE__, 1) . "/Views/Partial/Header.php";
?>
<?php include_once dirname(__FILE__, 1) . "/Views/Partial/Managment.Menu.php"; ?>
<main class="managment-wrapper" data-userid="<?php echo $_SESSION["userid"] ?>">
    <header class='clearfix'>
        <div class="hu-ui user">
            <img class="portrait" src=<?php echo BaseUrl . "/Control/Content/portrait.php" ?>>
            <a class="header" href='javascript:void(0)'><?php echo $_SESSION["name"] ?></a>
            <a class='logout' href=<?php echo BaseUrl . "/Control/Action/Action.logout.php" ?>>退出</a>
        </div>
    </header>
    <div id='managment' class="managment">
    </div>
</main>
</body>
<script type="application/javascript">
    $(function () {
        $(document).on("click", "div.menu-item", function () {
            var that = $(this);
            if (that.hasClass('active')) return;
            var type = that.data("type");
            var url = null;
            var baseUrl = '<?php echo BaseUrl . "/Views/Partial"?>';
            switch (type) {
                case "newscene":
                    url = baseUrl + "/Scene.Editor.php";
                    break;
                case "newhotel":
                    url = baseUrl + "/Hotel.Editor.php";
                    break;
                case "scenemanagment":
                    url = baseUrl + "/Scene.Managment.php";
                    break;
                case "hotelmanagment":
                    url = baseUrl + "/Hotel.Managment.php";
                    break;
                case "state":
                    url = baseUrl + "/SysState.Managment.php";
                    break;
                case "sysmanagment":
                    url = baseUrl + "/Sys.Managment.php";
                    break;
                default:
                    url = "";
            }
            ToggleMenuItem(that);
            loadModule(url);
        });
        var loadModule = function (url) {
            if (url == "") {
                console.error("url 地址不正确");
                return;
            }
            $.get(url, {}, function (respond) {
                var managmentEle = $("#managment");
                if (managmentEle.children().length > 0) managmentEle.children().remove();
                managmentEle.append(respond);
            }).fail(function (respond) {
                var dom = "<div class='ui info message error'>内容加载失败</div>";
                var managmentEle = $("#managment");
                if (managmentEle.children().length > 0) managmentEle.children().remove();
                managmentEle.append(dom);
            })
        };
        var ToggleMenuItem = function (target) {
            $(".menu-item", $(document)).removeClass("active");
            target.addClass("active");
        };
        $("div.menu-item").first().click();
    });
</script>
<script type="text/javascript">
    $(function () {
        $(document).on('click', "td.action .modify", function (evt) {
            var that = $(this);
            $("#modify").modal({
                closeable: true,
                onShow: function () {
                    var wrapper = $(this);
                    var id = that.parents("tr").data("id");
                    var url = "<?php echo $BaseUrl . "/Views/Partial/modify.php"?>";
                    $.get(url, {"id": id}, function (html) {
                        if (wrapper.children().length > 0) wrapper.children().remove();
                        wrapper.append(html);
                    })
                },
                onApprove: function () {
                    var url = "<?php echo $BaseUrl . '/Control/Action/Managment.Action.User.php'?>";
                    var inputGroup = $(".auth input", $(this)).toArray();
                    var auth = {};
                    var postData = {};
                    inputGroup.forEach(item => {
                        auth[$(item).data("type")] = item.checked ? true : false;
                    });
                    var userName = $("#name", $(this)).val();
                    var trueName = $("#truename", $(this)).val();
                    var id = that.parents("tr").data("id");
                    if (userName == "" || trueName == "") {
                        alert("登录名或用户名不能为空");
                        return false;
                    }
                    var data = {"auth": auth, "user": userName, "name": trueName, "id": id};
                    postData.data = data;
                    postData.action = "update";
                    console.log(postData);
                    $.post(url, postData, function (respond) {
                        var Json = JSON.parse(respond);
                        if (Json.status) {
                            var sceneChecked = auth.scene ? "checked" : "";
                            var hotelChecked = auth.hotel ? "checked" : "";
                            var stateChecked = auth.state ? "checked" : "";

                            var DomStr = "<div class='ui checkbox'>" +
                                "<input type='checkbox' data-type='scene' " + sceneChecked + " disabled/>" +
                                "<label>景点</label>" +
                                "</div>" +
                                "<div class='ui checkbox'>" +
                                "<input type='checkbox' data-type='hotel' " + hotelChecked + " disabled/>" +
                                "<label>民宿</label>" +
                                "</div>" +
                                "<div class='ui checkbox'>" +
                                "<input type='checkbox' data-type='scene' " + stateChecked + " disabled/>" +
                                "<label>运行状态</label>";
                            var authDom = $(".auth", that.parents("tr"));
                            if (authDom.children().length > 0) authDom.children().remove();
                            authDom.append(DomStr);
                            alert("更新成功");
                        } else {
                            alert("更新失败");
                        }
                    })
                }
            }).modal("show");
        });
        $(document).on('click', 'td.action .modifypsd', function (evt) {
            var postData = {};
            var that = $(this);
            var url = "<?php echo $BaseUrl . '/Control/Action/Managment.Action.User.php'?>";
            $("#modifypsd").modal({
                closable: true,
                onShow: function () {
                    $("#psdInput", $(this)).val("");
                },
                onApprove: function (evt) {
                    var id = that.parents("tr").data("id");
                    var psd = $("#psdInput", $(this)).val();
                    if (psd == "") {
                        alert("密码不能为空");
                        return false;
                    }
                    var data = {"id": id, "psd": psd};
                    var postData = {};
                    postData.action = "modifypsd";
                    postData.data = data;
                    $.post(url, postData, function (respond) {

                        var json = JSON.parse(respond);
                        if (json.status) {
                            alert("修改成功");
                        } else {
                            alert("修改失败");
                        }
                    })
                }
            }).modal("show");
        });
        $(document).on('click', 'td.action.user .delete', function (evt) {
            var postData = {};
            var that = $(this);
            var url = "<?php echo $BaseUrl . '/Control/Action/Managment.Action.User.php'?>";
            var id = $(this).parents("tr").data("id");
            postData.action = "delete";
            postData.data = {"id": id};
            $.post(url, postData, function (respond) {
                var jsonData = JSON.parse(respond);
                if (!jsonData.status) {
                    alert("删除失败");
                } else {
                    that.parents("tr").remove();
                    alert("删除成功");
                }
            })
        });
        $(document).on('click', 'td.action.user .resetpsd', function (evt) {
            var url = "<?php echo $BaseUrl . '/Control/Action/Managment.Action.User.php'?>";
            var postData = {};
            var id = $(this).parents("tr").data("id");
            postData.action = "resetpsd";
            postData.data = {"id": id};
            $.post(url, postData, function (respond) {
                var json = JSON.parse(respond);
                if (!json.status) {
                    alert("重置失败");
                } else {
                    alert("重置成功");
                }
            })
        });
        $(document).on('click', "#adduser", function (evt) {
            $("#adduserdlg").modal({
                closeable: true,
                onShow: function () {
                    var userName = $("#name", $(this)).val("");
                    var trueName = $("#truename", $(this)).val("");
                    var inputs = $(".auth input", $(this)).toArray();
                    inputs.forEach(function (item) {
                        item.checked = false;
                    });
                },
                onApprove: function (evt) {
                    var url = "<?php echo $BaseUrl . '/Control/Action/Managment.Action.User.php'?>";
                    var auth = {};
                    var postData = {};
                    var inputGroup = $(".auth input", $(this)).toArray();
                    inputGroup.forEach(item => {
                        auth[$(item).data("type")] = item.checked ? true : false;
                    });
                    var userName = $("#name", $(this)).val();
                    var trueName = $("#truename", $(this)).val();
                    var data = {auth: auth, user: userName, name: trueName};
                    postData.data = data;
                    postData.action = "add";
                    if (userName == "" || trueName == "") {
                        alert("登录名或用户名不能为空");
                        return false;
                    }
                    $.post(url, postData, function (respond) {
                        var Json = JSON.parse(respond);
                        if (!Json.status) {
                            alert("添加失败");
                        } else {
                            if ($("tbody tr").eq(0).attr("id") == "message") {
                                $("tbody tr").remove();
                            }
                            var id = Json.id;
                            var time = Json.time;
                            var len = $("tbody tr").length;
                            var sceneChecked = postData.data.auth.scene ? "checked" : "";
                            var hotelChecked = postData.data.auth.hotel ? "checked" : "";
                            var stateChecked = postData.data.auth.state ? "checked" : "";
                            var str = "<tr data-id=" + id + ">" +
                                "<td>" + (++len) + "</td>" +
                                "<td class='user'>" + postData.data.user + "</td>" +
                                "<td class='tname'>" + postData.data.name + "</td>" +
                                "<td class='ctime'>" + Json.ctime + "</td>" +
                                "<td class='auth'>" +
                                "<div class='ui checkbox'>" +
                                "<input type='checkbox' data-type='scene' " + sceneChecked + " disabled/>" +
                                "<label>景点</label>" +
                                "</div>" +
                                "<div class='ui checkbox'>" +
                                "<input type='checkbox' data-type='hotel' " + hotelChecked + " disabled/>" +
                                "<label>民宿</label>" +
                                "</div>" +
                                "<div class='ui checkbox'>" +
                                "<input type='checkbox' data-type='scene' " + stateChecked + " disabled/>" +
                                "<label>运行状态</label>" +
                                "</div>" +
                                "</td>" +
                                "<td class='action'>" +
                                "<span class='hu-ui action-text delete' data-action='delete'>删除</span>" +
                                "<span class='hu-ui action-text modify' data-action='modify'>修改名称</span>" +
                                "<span class='hu-ui action-text reset' data-action='resetpsd'>重置密码</span>" +
                                "</td>";
                            $("tbody").append(str);
                        }
                    });
                }
            }).modal("show");
        });
        var refleshData = function () {
            var url = "<?php echo $BaseUrl . '/Control/Action/Managment.Action.User.php'?>";
            $.post(url, data, function (respondText) {
                var respondData = JSON.parse(respondText);
                if (respondData.status) {
                    $("table tbody").append(respondData.html);
                } else {
                    //失败
                }
            });
        };
    })
</script>
<script type="application/javascript">
    $(function () {
        //debugger;
        $(document).on("click", "#save-config", function () {
            var that = $(this);
            var url = "<?php echo $BaseUrl . '/Control/Action/Action.Config.php'?>";
            var configData = {};
            var input = $("input.config").toArray();
            input.forEach(function (ele) {
                var item = $(ele);
                configData[item.data('title')] = item.val();
            });
            postData = {};
            postData.data = configData;
            $.post(url, postData, function (respond) {
                var data = JSON.parse(respond);
                if (data.status) {
                    that.addClass("disabled");
                    $(".table-wrapper .input").addClass("disabled");
                    $(".table-wrapper .dropdown").addClass("disabled");
                    alert("保存成功");
                } else {
                    that.unbind("click").bind("click");
                    alert("保存失败");
                }
            })
        })
    })
</script>
<script type='text/javascript'>
    $(function () {
        /**
         *    0 is represent culture scene
         *    1 is represent nature scene
         **/
        $(document).on('click', ".pagination .item", function (evt) {
            debugger;
            var url = ' <?php echo $BaseUrl . "/Control/Managment.Data.Scene.php"?>';
            var that = $(this);
            var currentPage = $('.pagination .active').data('page');
            var currentActiveItem = $('.pagination .active');
            var maxPage = $('tfoot tr').data('maxpage');

            $('.pagination .item').removeClass('active');
            if (that.attr('id') == 'nextPage') {
                //如果当前页为最后一页
                if (currentPage == maxPage) {
                    alert('已经是最后一页了');
                    currentActiveItem.addClass('active');
                    $("#prevPage").insertAfter(DomStr);

                } else {
                    //如果当前页为当前索引的最后一页
                    $;
                    if (currentActiveItem.next().hasClass('icon')) {
                        var DomStr = Regernatepagination(0, currentPage + 1, maxPage);
                        if (DomStr.change) {
                            $(".pagination").children().remove();
                            $(".pagination").append(DomStr.DomString);
                        }
                    } else {
                        currentActiveItem.next().addClass('active');
                    }
                    FetchData(sceneType, currentPage + 1, state);
                }

            } else if (that.attr('id') == 'prevPage') {
                //如果当前页为最后一页
                if (currentPage == 1) {
                    alert("已经是第一页了");
                    currentActiveItem.addClass('active');

                } else {
                    //如果当前页为当前索引的第一页
                    if (currentActiveItem.prev().hasClass('icon')) {
                        var DomStr = Regernatepagination(1, currentPage - 1, maxPage);
                        if (DomStr.change) {
                            $(".pagination").children().remove();
                            $(".pagination").append(DomStr.DomString);
                        }
                    } else {
                        currentActiveItem.prev().addClass('active');
                    }
                    FetchData(sceneType, currentPage - 1, state);
                }

            } else {
                if (that.hasClass('active')) return;
                that.addClass('active');
                var pageNum = that.data('page');
                var DomStr = Regernatepagination(2, that.data('page'), maxPage);
                if (DomStr.change) {
                    $(".pagination").children().remove();
                    $(".pagination").append(DomStr.DomString);
                }
                FetchData(sceneType, pageNum);
            }
        });

        var Regernatepagination = function (type, currentPageNum, maxPage) {
            // type
            //  0 is next page
            //  1 is prev page
            //  2 is number page
            // init with head prevPage
            var DomString = '<a class="icon item" id="prevPage"><i class="left chevron icon" ></i></a >';
            var arr = null;
            switch (type) {
                case 0:
                    arr = [currentPageNum - 4, currentPageNum - 3, currentPageNum - 2, currentPageNum - 1, currentPageNum];
                    break;
                case 1:
                    arr = [currentPageNum, currentPageNum + 1, currentPageNum + 2, currentPageNum + 3, currentPageNum + 4];
                    break;
                case 2:
                    if (currentPageNum + 2 >= maxPage) {
                        arr = [maxPage - 4, maxPage - 3, maxPage - 2, maxPage - 1, maxPage];
                    } else if (currentPageNum - 2 <= 1) {
                        arr = [1, 2, 3, 4, 5];
                    } else {
                        arr = [currentPageNum - 2, currentPageNum - 1, currentPageNum, currentPageNum + 1, currentPageNum + 2];
                    }
                    break;
                default:
                    arr = []
            }
            arr.forEach(function (item) {
                if (item == currentPageNum) {
                    DomString += "<a class='item active' data-page=" + item + ">" + item + "</ a>";
                } else {
                    DomString += "<a class='item' data-page=" + item + ">" + item + "</ a>";
                }
            });
            DomString += '<a class="icon item" id="nextPage"><i class="right chevron icon"></i></a>';
            if (arr.length == 0) change = false;
            else change = true;
            return {"change": change, 'DomString': DomString};
        };
        //reflesh data;
        $(document).on('click', "#reflesh", function (evt) {
            var state = $("table").data("state");
            var currentPage = parseInt($("tfoot tr").data('currentpage'));
            FetchData(currentPage, state);
        });
        //action
        $(document).on('click', '.action.scene.hotel span', function () {
            var that = $(this);
            var id = that.parents('tr').data('id');
            var url = '<?php echo $BaseUrl . "/Control/Action/Managment.Action.Scene.php"?>';
            var actionType = that.data('action');
            switch (actionType) {
                case 'pull':
                    $.post(url, {'id': id, 'action': 'pull'}, function (respond) {
                        var Data = JSON.parse(respond);
                        if (Data.status) {
                            that.parents("tr").remove();
                            alert("操作成功");
                        } else {
                            alert("操作失败");
                        }
                    });
                    break;
                case 'waste':
                    $.post(url, {'id': id, 'action': 'waste'}, function (respond) {
                        var Data = JSON.parse(respond);
                        if (Data.status) {
                            that.parents("tr").remove();
                            alert("操作成功");
                        } else {
                            alert("操作失败");
                        }
                    });
                    break;
                case 'restore':
                    $.post(url, {'id': id, 'action': 'restore'}, function (respond) {
                        var Data = JSON.parse(respond);
                        if (Data.status) {
                            that.parents("tr").remove();
                            alert("操作成功");
                        } else {
                            alert("操作失败");
                        }
                    });
                    break;
                case 'delete':
                    $.post(url, {'id': id, 'action': 'delete'}, function (respond) {
                        var Data = JSON.parse(respond);
                        if (Data.status) {
                            that.parents("tr").remove();
                            alert("操作成功");
                        } else {
                            alert("操作失败");
                        }
                    });
                    break;
                default:
                    alert('未知操作');
            }
        });
        var FetchData = function (page, state) {
            var url = '<?php echo BaseUrl . "/Control/Data/Managment.Data.Scene.php"?>';
            $.get(url, {page: page, state: state}, function (respondData) {
                $("#loader").remove();
                if ($("table tbody").children().length > 0) $("table tbody").children().remove();
                $("table tbody").append(respondData);
            });
        };
        //首次加载的时候需要加载数据
    })
</script>
<script type="application/javascript">
    $(function () {
        $(document).on("click", "#set-city", function () {
            var url = '<?php echo BaseUrl . "/Control/Action/SetCity.php"?>';
            var that = $(this);
            var city = $("#city-input").val();
            $.post(url, {"city": city}, function (respond) {
                var json = JSON.parse(respond);
                if (json.status) {
                    that.addClass("disabled");
                    $("#city-input").parents(".input").addClass("disabled");
                    alert("设置成功");
                } else {
                    alert("设置失败");
                }
            }).fail(function () {
                alert("设置失败");
            });
        });
    })
</script>
</html>

