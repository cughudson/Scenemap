<?php
header("Content-Type:text/html; charset=utf8");
$htmlTitle = "照片浏览地图";
require_once dirname(__FILE__, 1) . "/Views/Partial/Header.php";
include_once dirname(__FILE__, 1) . "/Control/Lib/ConfigManagment.php";
$fileDir = dirname(__FILE__, 1) . "/Config.ini";
$Config = new ConfigManagment($fileDir);
$city = $Config->Parse()['city'];
?>
</head>
<body>
<script type="application/javascript" src=<?php echo BaseUrl . "/Assert/js/BMap.js" ?>></script>
<script type="application/javascript" src=<?php echo BaseUrl . "/Assert/js/qrcode.min.js" ?>></script>
<main id="map-cantainer" data-bounds={}>
    <div id='right-panel-wrapper' class="open">
        <header>
                <span class="close-btn">
                    <svg fill="#ffffff" height="36" viewBox="0 0 24 24" width="36" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                        <path d="M0 0h24v24H0z" fill="none"/>
                    </svg>
                </span>
            <span class="size-control">
                    <svg fill="#ffffff" xmlns="http://www.w3.org/2000/svg" version="1.1" width="36" height="36"
                         viewBox="0 0 24 24">
                        <path d="M10,21V19H6.41L10.91,14.5L9.5,13.09L5,17.59V14H3V21H10M14.5,10.91L19,6.41V10H21V3H14V5H17.59L13.09,9.5L14.5,10.91Z"/>
                    </svg>
                    <svg fill="#ffffff" xmlns="http://www.w3.org/2000/svg" version="1.1" width="36" height="36"
                         viewBox="0 0 24 24">
                        <path d="M19.5,3.09L15,7.59V4H13V11H20V9H16.41L20.91,4.5L19.5,3.09M4,13V15H7.59L3.09,19.5L4.5,20.91L9,16.41V20H11V13H4Z"/>
                    </svg>
                </span>
        </header>
        <div id="right-panel">
            <div id="article-wrapper">
                <article>
                    <span class="hint"><i class="icon info circle"></i>点击地图中的标签才能够查看内容</span>
                </article>
            </div>
            <div class="copy-right">
                <span></span>
            </div>
        </div>
    </div>
    <div id="map" style='width:100%;height:100%'></div>
    <div class="map-toolbar">
        <span class="btn clickable" title="关于我们" id="about-btn"><i class="icon user"></i></span>
        <span class="btn clickable" title="热门推荐" id="recommand-btn"><i class="icon content"></i></span>
        <span class="btn clickable" title="操作手册" id="help-btn"><i class="help circle outline icon"></i></span>
    </div>
</main>
<div class="modal ui mini" id="qcode-dlg">
    <div class="content" style="text-align: center;padding: 32px">
        <div class="qcode-wrapper" id="qrcode" style="display: inline-block;padding: 12px"></div>
        <div class="hint">用微信扫描上面的二维码进行分享</div>
    </div>
</div>
<div class="modal ui about-us">
</div>
<div class="modal ui help">
</div>
</body>
<script type="application/javascript">
    $(function () {
        $(document).on("click", ".close-btn", function () {
            var panel = $(this).parents("#right-panel-wrapper");
            if (panel.hasClass("open")) {
                panel.removeClass("open");
                panel.addClass("close");
                panel.removeClass("maximize");
            } else {
                panel.removeClass("close");
                panel.addClass("open");
                panel.removeClass("maximize");
            }
        });
        $(document).on("click", ".weixin", function () {
            $("#qcode-dlg").modal({
                closeable: true,
                onShow: function () {
                    var id = $("article").data("id");
                    var type = $("article").data("type");
                    var qrCodeUrl = location.origin + "/detail.php?id=" + id + "&type=" + type;
                    console.log(qrCodeUrl);
                    MapScope.CreateQrCode("qrcode", qrCodeUrl);
                }
            }).modal("show");
        });
        $(document).on("click", ".size-control", function () {
            var panel = $(this).parents("#right-panel-wrapper");
            if (panel.hasClass("maximize")) {
                panel.removeClass("maximize");
            } else {
                panel.addClass("maximize");
            }
        })
    })
</script>
<script type="application/javascript">
    $(function () {
        window.MapScope = {};
        MapScope.FetchMapData = function (url, bounds, callback, failed) {
            $.get(url, {"bounds": bounds}, function (respondData) {
                var json = JSON.parse(respondData);
                if (json.status) {
                    var data = json.data;
                    var overlayArr = [];
                    //当前地图中所具有的图标
                    HuMap.map.getOverlays().forEach(item => {
                        overlayArr.push(item.point);
                    });
                    data.forEach(function (item) {
                        var MapBounds = HuMap.ToMapBounds(bounds);
                        var pt = HuMap.CreatePt(item.lng, item.lat);
                        var isExist = IsInOverlay(pt, overlayArr);
                        var bInBounds = HuMap.IsInBounds(MapBounds, pt);
                        if (!isExist || overlayArr.length == 0) {
                            HuMap.CreateMarker2(item, function (marker) {
                                callback(marker);
                            });
                        }
                    })
                } else {
                    failed();
                }
            })
        };
        MapScope.GetBoundFromPosArray = function (data) {
            var lats = [];
            var lngs = [];
            data.forEach(function (item) {
                lats.push(item.lat);
                lngs.push(item.lng);
            });
            lats.sort();
            lngs.sort();

            var swg = lngs[0];
            var neg = lngs[lngs.length - 1];
            var swt = lats[0];
            var net = lats[lats.length - 1];
            var swPt = new BMap.Point(swg, swt);
            var nePt = new BMap.Point(neg, net);
            var bounds = new BMap.Bounds(swPt, nePt);
            return bounds;
        };
        MapScope.FetchIntroContent = function (url, id, type) {
            var panelContent = $("article");
            var rightPanel = $("#right-panel-wrapper");
            var currentId = $("article").data("id");
            if (parseInt(currentId) == id) return;

            $.get(url, {"id": id, "type": type}, function (html) {

                if (panelContent.length > 0) {
                    panelContent.fadeOut(200, function () {
                        panelContent.remove();
                        setTimeout(function () {
                            $(html).appendTo($("#article-wrapper")).hide().fadeIn(200)
                        }, 400);
                    });
                } else {
                    setTimeout(function () {
                        $(html).appendTo($("#article-wrapper")).hide().fadeIn(200)
                    }, 400);
                }
                if (rightPanel.hasClass("open")) {
                    rightPanel.addClass("close");
                    rightPanel.removeClass("open");
                }
            });
        };
        var IsInOverlay = function (pt, Pts) {

            for (var i = 0; i < Pts.length; i++) {
                if (pt.equals(Pts[i])) return true;
                if (i == Pts.length) return false;
            }
        };
        MapScope.CreateQrCode = function (id, url) {
            //debugger;
            if ($("#" + id).children().length > 0) $("#" + id).children().remove();
            var qrCode = new QRCode(id, {
                text: "",
                width: 120,
                height: 120,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
            qrCode.clear();
            qrCode.makeCode(url);
        }
    })
</script>
<script type="application/javascript">
    $(function () {

        var mapUrl = "<?php echo BaseUrl . '/Control/Data/MapData.php'?>";
        var contentUrl = "<?php echo BaseUrl . "/Control/Content/Intro.php"?>";
        var city = "<?php echo $city ?>";

        var id = parseInt(window.top.location.search.replace("?", "").split("=")[1]);
        if (isNaN(id)) {
            HuMap.init("map", city, 11);
        } else {
            HuMap.init("map", city, 11);
            $.get(mapUrl, {id: id}, function (respond) {
                var item = JSON.parse(respond).data;
                if (item.length == 0) {
                    //然后加载其他数据
                    setTimeout(function () {
                        var bounds = HuMap.getBounds();
                        MapScope.FetchMapData(mapUrl, bounds, function (item) {
                            MapScope.FetchIntroContent(contentUrl, item.id, item.type);
                        }, function () {
                            console.error("数据加载失败");
                        });
                    }, 1000);
                } else {
                    var pt = new BMap.Point(item[0].lng, item[0].lat);
                    HuMap.init("map", pt, 11);
                    HuMap.CreateMarker2(item[0], function (marker) {
                        MapScope.FetchIntroContent(contentUrl, marker.id, marker.type);
                    });
                    setTimeout(function () {
                        HuMap.map.panTo(marker.point)
                    }, 200);
                    setTimeout(function () {
                        if ($(window).width() > 768) {
                            var WinPixel = ($("#map").width() - $("#right-panel-wrapper").width()) < 0 ? 0 : $("#map").width() - $("#right-panel-wrapper").width();
                            HuMap.map.panBy($("#map").width() / 2 - WinPixel / 2, 0, true);
                        } else {
                            var WinPixel = ($("#map").height() - $("#right-panel-wrapper").height()) < 0 ? 0 : $("#map").height() - $("#right-panel-wrapper").height();
                            HuMap.map.panBy($("#map").height() / 2 - WinPixel / 2, 0, true);
                        }
                    }, 400);
                    var marker = HuMap.GetMarkerById(id);
                    marker.setAnimation(BMAP_ANIMATION_BOUNCE);
                    HuMap.currentMarker["active"].push(marker);
                    MapScope.FetchIntroContent(contentUrl, marker.id, marker.type);

                    MapScope.FetchMapData(mapUrl, HuMap.getBounds(), function (item) {
                        MapScope.FetchIntroContent(contentUrl, item.id, item.type);
                    }, function () {
                        console.error("数据加载失败");
                    });
                }
            }).fail(function () {
                console.log("加载失败");
            });
        }
    })
</script>
<script type='text/javascript'>
    $(function () {
        var RightPanel = $("#right-panel");
        var mapContainer = $("#map-cantainer");
        var width = RightPanel.innerWidth();
        var openPanelIcon = $("#openpanel-icon");

        var mapUrl = "<?php echo BaseUrl . '/Control/Data/MapData.php'?>";
        var contentUrl = "<?php echo BaseUrl . "/Control/Content/Intro.php"?>";
        $("#open-panel").on('click', function (evt) {

            if (!RightPanel.hasClass("hasClose")) {
                RightPanel.animate({"left": -width}, 400, 'swing', function () {
                    RightPanel.addClass("hasClose");
                });
            } else {
                RightPanel.animate({"left": 0}, 400, 'swing', function () {
                    RightPanel.removeClass("hasClose");
                });
            }
        });
        $(document).on("click", "#gohere", function () {
            var that = $(this);
            var Pt = that.parents(".meta").data("location").split(';');
            var EndPt = new BMap.Point(parseFloat(Pt[1]), parseFloat(Pt[0]));

            if (!navigator.geolocation) return;
            navigator.geolocation.getCurrentPosition(function (data) {
                var pt = new BMap.Point(data.coords.longitude, data.coords.latitude);
                var marker = new BMap.Marker(pt);
                HuMap.map.addOverlay(marker);
                HuMap.map.panTo(pt);
            })
        });
        $("#help-btn").click(function () {

        });
        $("#about-btn").click(function () {

        });
        $("#recommand-btn").click(function () {

        });
        //注册事件
        HuMap.load(function (bounds) {
            HuMap.ZoomEnd(function (bounds) {
                HuMap.RemoveMarker(function (bounds, overlays) {
                    overlays.forEach(function (item) {
                        if (!bounds.containsPoint(item.point)) {
                            HuMap.map.removeOverlay(item);
                        }
                    })
                });
                MapScope.FetchMapData(mapUrl, bounds, function (item) {
                    MapScope.FetchIntroContent(contentUrl, item.id, item.type);
                }, function () {
                    console.error("数据加载失败");
                })
            });
            HuMap.MoveEnd(function (bounds) {

                HuMap.RemoveMarker(function (bounds, overlays) {
                    overlays.forEach(function (item) {
                        if (!bounds.containsPoint(item.point)) {
                            HuMap.map.removeOverlay(item);
                        }
                    })
                });
                MapScope.FetchMapData(mapUrl, bounds, function (item) {
                    MapScope.FetchIntroContent(contentUrl, item.id, item.type);
                }, function () {
                    console.error("加载数据失败");
                });
            });
            $(document).on("click", "i.chearup", function () {
                var itemId = $("article").data("id");
                var that = $(this);
                var url = "<?php echo BaseUrl . '/Control/Action/ChearUp.php'?>";
                $.post(url, {"id": itemId}, function (data) {
                        var obj = JSON.parse(data);
                        if (obj.status) {
                            $(that).css({"color": "red"});
                        } else {
                            alert("你已经点赞过该篇文章，且半个小时之内只能点赞一次");
                        }
                    }
                ).fail(function () {
                    alert("点赞失败");
                })
            });
        })
    })
</script>
<script type="application/javascript">
    var loadDefaultImage = function () {
        var tar = event.target;
        tar.src = "<?php echo BaseUrl . "/Assert/Resource/image/default.jpg"?>";
    }
</script>
<html>
