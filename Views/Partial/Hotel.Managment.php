<?php
/**
 * Created by PhpStorm.
 * User: cughu
 * Date: 2018/2/4
 * Time: 1:06
 */
define('BaseUrl', 'http://localhost:63342/HomeTown');
header("Content-Type:text/html; charset=utf8");
include_once dirname(__FILE__, 3) . "/Control/Lib/HotelManagment.php";
try {
    $Hotel = new HotelManagment();
} catch (Exception $ex) {
    echo "<script type='application/javascript'>alert('加载失败')</script>";
    echo "<div class='ui message info error'>加载失败</div>";
    die();
}
//$rowPerPage = (int)$Hotel->configData['rowPerPage'];
//$total = $Scene->GetTotalCount(0);
//$maxPage = ceil($total/$rowPerPage);
$maxPage = 4;
?>
<section id='tab-group'>
    <div class="table-header">
        <div class="ui top attached tabular menu">
            <a class="item active" data-state="publish">已发布</a>
            <a class="item" data-state="draft">草稿箱</a>
            <a class="item" data-state="waste">回收箱</a>
            <div class='button-group'>
                <div class='reflesh button' id='reflesh'>
                    <i class='icon refresh'></i>
                </div>
            </div>
        </div>
    </div>
    <div class='table-wrapper'>
        <table class="ui celled table" data-currentpage=4 data-maxpage=36>
            <thead>
            <tr>
                <th>序列</th>
                <th>标题</th>
                <th>作者</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($row == 0) { ?>
                <tr>
                    <td colspan='5'>
                        <div class="ui info message">未检索到任何数据</div>
                    </td>
                </tr>
            <?php } ?>
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
</div>
<script type="application/javascript">
    $(function () {
        var url = "<?php echo BaseUrl . "/Control/Tab/HotelFooter.php"?>";
        $(".tabular .item").click(function (evt) {
            var that = $(this);
            var state = that.data('state');
            if (that.hasClass('active')) return;
            //toggle
            $(".tabular .item").removeClass('active');
            that.addClass('active');

            $("table").data('state', state);
            $.post(url, {"state": type}, function (respond) {
                var json = JSON.parse(respond);
                if (json.status) {
                    if ($("table tfoot").children().length > 0) $("table tfoot").children().remove();
                    $("table tfoot").append(json.html);
                } else {
                    alert("加载失败");
                }
            })

        })
    })
</script>
</main>
<script type='text/javascript'>
    $(function () {
        /**
         *    0 is represent culture scene
         *    1 is represent nature scene
         **/
        $(document).on('click', ".pagination .item", function (evt) {
            var that = $(this);
            var currentPage = $('.pagination .active').data('page');
            var currentActiveItem = $('.pagination .active');
            var maxPage = $('table').data('maxpage');
            $('.pagination .item').removeClass('active');
            if (that.attr('id') == 'nextPage') {
                //如果当前页为最后一页
                if (currentPage == maxPage) {
                    alert('已经是最后一页了');
                    currentActiveItem.addClass('active');
                    $("#prevPage").insertAfter(DomStr);

                } else {
                    //如果当前页为当前索引的最后一页
                    if (currentActiveItem.next().hasClass('icon')) {
                        var DomStr = Regernatepagination(0, currentPage + 1, maxPage);
                        if (DomStr.change) {
                            $(".pagination").children().remove();
                            $(".pagination").append(DomStr.DomString);
                        }
                    } else {
                        currentActiveItem.next().addClass('active');
                    }
                    FetchData(sceneType, currentPage + 1);
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
                    FetchData(sceneType, currentPage - 1);
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
            arr.forEach(funtion(item);
            {
                if (item == currentPageNum) {
                    DomString += "<a class='item active' data-page=" + item + ">" + item + "</ a>";
                } else {
                    DomString += "<a class='item' data-page=" + item + ">" + item + "</ a>";
                }
            }
        )
            DomString += '<a class="icon item" id="nextPage"><i class="right chevron icon"></i></a>';
            if (arr.length == 0) change = false;
            else change = true;
            return {"change": change, 'DomString': DomString};
        };
        //reflesh data;
        $("#reflesh").on('click', function (evt) {
            var sceneType = $('#scenetype')[0].checked ? 0 : 1;
            var currentPage = $(".pagination .active").data('page');
            FetchData(sceneType, currentPage);
        });
        $("input[type='radio']").on('click', function (evt) {
            var sceneType = $('#scenetype')[0].checked ? 0 : 1;
            var currentPage = $(".pagination .active").data('page');
            FetchData(sceneType, currentPage);
        });
        //action
        $(document).on('click', '.action span', function () {
            var id = $(this).parents('tr').data('id');
            var actionType = $(this).data('action');
            switch (actionType) {
                case 'pull':
                    $.post('../action.php', {'id': id}, function (respondData) {
                        var Data = JSON.parse(respondData);
                        if (Data.status) {
                            alert("操作成功");
                        } else {
                            alert("操作失败");
                        }
                    });
                    break;
                case 'delete':
                    $.post('../action.php', {'id': id}, function (respondData) {
                        var Data = JSON.parse(respondData);
                        if (Data.status) {
                            alert("操作成功");
                        } else {
                            alert("操作失败");
                        }
                    });
                    break;
                case 'restore':
                    $.post('../action.php', {'id': id, 'action': 'restore'}, function (respondData) {
                        var Data = JSON.parse(respondData);
                        if (Data.status) {
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

        var FetchData = function (sceneType, page) {
            $.post("../fetchData.php", {type: sceneType, page: page}, function (respondData) {
                var Data = JSON.parse(respondData);
                if (Data.status) {
                    $("#loader").remove();
                    $("table tbody").append(Data.html);
                } else {
                    $("#loader").remove();
                    var str = ' <tr><td colspan = 5 ><div class="ui message error"><p>加载数据失败</p></div></td ></tr >';
                }
            });
        }
    })
</script>
