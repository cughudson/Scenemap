//most require zepto and baidu map or google map first
//for baidumap
(function (win, doc) {
    var Map = {};
    /**
     * currentMarker['naturescene'] for naturescene marker
     * currentMarker['culturescene'] for culturescene marker
     * currentMarker['hotel'] for hotal marker
     */
    Map.currentMarker = {};
    Map.currentMarker["hotel"] = [];
    Map.currentMarker["naturescene"] = [];
    Map.currentMarker["culturescene"] = [];
    Map.currentMarker["active"] = [];

    /**
     *
     * @param {string} id
     * @param {string} city
     * @param {int} level
     */
    Map.init = function (id, city, level) {
        //0 is represent baidumap
        if (window.BMap == undefined) {
            console.error("BMap is not defined!");
            return;
        }
        var map = new BMap.Map(id);
        map.centerAndZoom(city, level);
        map.enableZoom = true;
        map.enableScrollWheelZoom();
        map.disableDoubleClickZoom();
        map.addControl(new BMap.MapTypeControl({
            mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP, BMAP_SATELLITE_MAP],
            anchor: BMAP_ANCHOR_TOP_RIGHT
        }));
        this.map = map;
    };
    /**
     *
     * @param {callback} func
     */
    Map.ZoomEnd = function (func) {
        var that = this;
        this.map.addEventListener('zoomend', function () {
            var bounds = that.getBounds();
            func(bounds);
        })
    };
    /**
     *
     * @param {callback} func
     */
    Map.MoveEnd = function (func) {
        var that = this;
        this.map.addEventListener('moveend', function () {
            var bounds = that.getBounds();
            func(bounds);
        })
    };
    /**
     *
     * @param {object} ptObjs object hold id and point of marker
     * @param {path} image the path to the image
     * @param {callback} func the function will invoke when user click the marker
     */
    Map.CreateMarker = function (item, imagePath, type, func) {

        var that = this;
        var pt = new BMap.Point(item.lng, item.lat);
        var Icon = new BMap.Icon(imagePath, new BMap.Size(100, 100));
        var marker = new BMap.Marker(pt, {icon: Icon});
        marker.id = item.id;
        marker.type = item.type;
        marker.title = item.title;
        marker.ctime = item.ctime;
        marker.surface = item.surface;

        that.map.addOverlay(marker);
        var that = this;
        var pt = new BMap.Point(item.lng, item.lat);
        var marker = new BMap.Marker(pt);

        that.map.addOverlay(marker);
        that.map.pt = pt.toString();
        marker.addEventListener("click", function (evt) {
            var ma = this;
            that.map.panTo(ma.point, false);
            var lastMarker = that.currentMarker["active"][0];
            that.currentMarker["active"].pop();
            if (lastMarker) lastMarker.setAnimation(null);
            that.currentMarker["active"].push(ma);
            setTimeout(function () {
                ma.setAnimation(BMAP_ANIMATION_BOUNCE);
            }, 400);
            setTimeout(function () {
                func(ma);
            }, 700);

        });
        marker.addEventListener("mouseover", function (evt) {
            var ma = this;
            var html = "<div class='info-content-wrapper' style='width:324px;'>" +
                "<img style='width: 128px;height: 72px;display: inline-block;float:left;padding-right: 12px' src=" + "./Assert/Resource/image/" + ma.surface + " onerror='loadDefaultImage()'/>" +
                "<div cass='content-wrapper'>" +
                "<h3 style='margin-bottom:12px;color:#545454;font-weight: normal;text-overflow: ellipsis;overflow: hidden;white-space: nowrap'>" + ma.title + "</h3>" +
                "<p class='meta clearfix' style='font-size: 12px'>" +
                "<span id='author' style='padding-right: 8px'>作者:&nbsp;" + ma.author + "</span>" +
                "<span id='posttime'>发布于:&nbsp;" + ma.ctime + "</span>" +
                "<a href='javascript:void(0)' style='display: block;padding: 6px 0'>(单击图标查看详情)</a>" +
                "</p>" +
                "</div>" +
                "</div>";
            var InfoWindow = new BMap.InfoWindow(html);
            setTimeout(function () {
                ma.openInfoWindow(InfoWindow);
            }, 500);
        });
        marker.addEventListener("mouseout", function (evt) {
            var mar = this;
            mar.closeInfoWindow();
        })
    };
    Map.CreateMarker2 = function (item, func) {
        var that = this;
        var pt = new BMap.Point(item.lng, item.lat);
        var marker = new BMap.Marker(pt);
        //marker.pt = pt.toString()
        marker.id = item.id;
        marker.type = item.type;
        marker.title = item.title;
        marker.ctime = item.ctime;
        marker.author = item.author;
        marker.surface = item.surface;

        that.map.addOverlay(marker);
        that.map.pt = pt.toString();
        marker.addEventListener("click", function (evt) {
            var ma = this;
            that.map.panTo(ma.point, false);
            if (that.currentMarker["active"].length == 0) {
                that.currentMarker["active"].push(ma);
                setTimeout(function () {
                    ma.setAnimation(BMAP_ANIMATION_BOUNCE);
                }, 400);
            } else {
                var lastMarker = that.currentMarker["active"][0];
                that.currentMarker["active"].pop();
                that.currentMarker["active"].push(ma);
                lastMarker.setAnimation(null);
                setTimeout(function () {
                    ma.setAnimation(BMAP_ANIMATION_BOUNCE);
                }, 400);
            }
            setTimeout(function () {
                func(ma);
            }, 700);
        });
        marker.addEventListener("mouseover", function (evt) {
            var ma = this;
            var html = "<div class='info-content-wrapper' style='width:324px;'>" +
                "<img style='width: 128px;height: 72px;display: inline-block;float:left;padding-right: 12px' src=" + "./Assert/Resource/image/" + ma.surface + " onerror='loadDefaultImage()' />" +
                "<div cass='content-wrapper'>" +
                "<h3 style='margin-bottom:12px;color:#545454;font-weight: normal;text-overflow: ellipsis;overflow: hidden;white-space: nowrap'>" + ma.title + "</h3>" +
                "<p class='meta clearfix' style='font-size: 12px'>" +
                "<span id='author' style='padding-right: 8px'>作者:&nbsp;" + ma.author + "</span>" +
                "<span id='posttime'>发布于:&nbsp;" + ma.ctime + "</span>" +
                "<a href='javascript:void(0)' style='display: block;padding: 6px 0'>(单击图标查看详情)</a>" +
                "</p>" +
                "</div>" +
                "</div>";
            var InfoWindow = new BMap.InfoWindow(html);
            ma.openInfoWindow(InfoWindow);
        });
        marker.addEventListener("mouseout", function (evt) {
            var mar = this;
            mar.closeInfoWindow();
        })
    };
    Map.DisableUserControl = function () {
        var map = this.map;
        map.disableScrollWheelZoom();
        map.disableDoubleClickZoom();
        map.disableDragging();
        map.disableKeyboard();
    };
    Map.CreateMarker3 = function (item, func) {
        var that = this;
        var pt = new BMap.Point(item.lng, item.lat);

        var marker = new BMap.Marker(pt);
        marker.id = item.id;
        marker.type = item.type;
        marker.title = item.title;
        marker.ctime = item.ctime;
        marker.author = item.author;
        marker.surface = item.surface;
        that.map.addOverlay(marker);

        func(marker);
    };
    Map.IsInBounds = function (bounds, point) {
        return bounds.containsPoint(point);
    };
    Map.CreatePt = function (lng, lat) {
        return new BMap.Point(lng, lat);
    };
    /**
     *
     * @param {string} filter
     */
    Map.GetMarkerById = function (id) {

        var EleArr = this.map.getOverlays();
        var returnData = [];

        for (var i = 0; i < EleArr.length; i++) {
            if (EleArr[i].id == id) {
                return EleArr[i];
            }
        }
        return false;
    };
    /**
     *
     * @param {string} filter
     */
    Map.hideMarker = function (filter) {
        var EleArr = this.map.getOverlay();
        EleArr.forEach(ele => {
            if (ele.type == filter) {
                ele.style.display = "none";
            }
        })
    };
    /**
     *
     * @param {string} filter
     */
    Map.ShowMarker = function (filter) {
        var EleArr = this.map.getOverlay();
        EleArr.forEach(ele => {
            if (ele.type == filter) {
                ele.style.display = "block";
            }
        });
    };
    /**
     *
     * @param {string} filter
     */
    Map.RemoveMarker = function (filter) {
        var EleArr = this.map.getOverlay();
        EleArr.forEach(ele => {
            if (ele.type == filter) {
                this.map.removeOverlay(ele);
            }
        });
    };
    /**
     * 源坐标类型(FROM)：
     *  1：GPS设备获取的角度坐标，wgs84坐标;
     *  2：GPS获取的米制坐标、sogou地图所用坐标;
     *  3：google地图、soso地图、aliyun地图、mapabc地图和amap(高德地图)地图所用坐标，国测局（gcj02）坐标;
     *  4：3中列表地图坐标对应的米制坐标;
     *  5：百度地图采用的经纬度坐标;
     *  6：百度地图采用的米制坐标;
     *  7：mapbar地图坐标;
     *  8：51地图坐标
     * TO:
     * 5：bd09ll(百度经纬度坐标),
     * 6：bd09mc(百度米制经纬度坐标);
     *
     * @param {BMPoint} point
     * @param {int} from
     * @param {int} to
     * @param {callback} success
     * @param {callback} failure
     */
    Map.ConvertCoord = function (point, from, to, success, failure) {
        var convertor = new BMap.Convertor();
        convertor.translate(point, from, to, function (data) {
            if (data.status == 0) {
                success(data.points[0]);
            } else {
                console.error("坐标转换发生错误");
            }
        });
    };
    /**
     *
     * @param {*} callback
     */
    Map.RemoveAllMarker = function (callback) {
        this.map.clearOverlays();
        callback();
    };
    Map.RemoveMarker = function (callback) {
        var bounds = this.map.getBounds();
        var overlays = this.map.getOverlays();
        callback(bounds, overlays);
    };
    /**
     *
     * @param {string} city
     */
    Map.setCurrentCity = function (city) {
        var level = this.map.getZoom();
        this.map.centerAndZoom(city, level);
    };
    Map.WGS2Baidu = function (pt) {
        //TODO
    };
    /**
     *
     * @param {*} pt
     * @param {*} func
     */
    Map.GetCityFromPoint = function (pt, func) {
        var geoCoder = new Map.GeoCoder();
        geoCoder.getLocation(pt, function (result) {
            return result.addressComponents;
        })
    };
    Map.Google2Baidu = function (pt) {
        this.WGS2Baidu();
    };
    Map.load = function (callback) {
        var that = this;
        that.map.addEventListener('load', function () {
            var bounds = that.getBounds();
            callback(bounds);
        });
    };
    /**
     * get the bounds of the map in json format
     */
    Map.ToMapBounds = function (boundsJson) {
        var swPt = new BMap.Point(boundsJson.swg, boundsJson.swt);
        var nePt = new BMap.Point(boundsJson.neg, boundsJson.net);
        return new BMap.Bounds(swPt, nePt);
    };
    Map.getBounds = function () {
        var bounds = this.map.getBounds();
        var boundsJSON = {
            "swg": bounds.getSouthWest().lng,
            "swt": bounds.getSouthWest().lat,
            "neg": bounds.getNorthEast().lng,
            "net": bounds.getNorthEast().lat,
        };
        return boundsJSON;
    };
    win.HuMap = Map;
})(window, document);