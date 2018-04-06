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
    Map.currentMarker["culturescene"] = []

    /**
     *
     * @param {string} id
     * @param {string} city
     * @param {int} level
     */
    Map.init = function (id, city, level) {
        //0 is represent baidumap
        var map = new BMap.Map(id);
        map.centerAndZoom(city, level);
        map.enableZoom = true;
        map.enableScrollWheelZoom();
        map.disableDoubleClickZoom();
        map.addControl(new BMap.MapTypeControl({
            mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP],
            anchor: BMAP_ANCHOR_TOP_RIGHT
        }));
        this.map = map;
    }
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
    Map.CreateMarker = function (ptObjs, image, type, func) {

        objs.forEach(item => {

            var pt = new BMap.Point(item.lng, item.lat);
            var Icon = new BMap.Icon(image, new BMap.Size(100, 100));
            var marker = new BMap.Marker(pt, {icon: Icon});
            marker.type = item.type;
            marker.id = item.id;
            this.map.addOverlay(marker);
            marker.addEventListener("click", function (evt) {
                func(this);
            });
        });
    };
    Map.CreateMarker2 = function (ptObjs, func) {

        ptObjs.forEach(item => {

            var pt = new BMap.Point(item.lng, item.lat);
            var marker = new BMap.Marker(pt);
            marker.pt = pt.toString();
            this.map.addOverlay(marker);
            this.map.pt = pt.toString();
            marker.addEventListener("click", function (evt) {
                func(this);
            });
        });
    }
    /**
     *
     * @param {string} filter
     */
    Map.GetMarker = function (filter) {

        var EleArr = this.map.getOverlay();
        var returnData = [];

        EleArr.forEach(ele => {
            if (ele.type == filter) {
                returnData.push(ele);
            }
        })
        return returnData;
    }
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
    }
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
    }
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
    }
    /**
     *
     * @param {*} callback
     */
    Map.RemoveAllMarker = function (callback) {
        this.map.clearOverlays();
        callback();
    }
    /**
     *
     * @param {string} city
     */
    Map.setCurrentCity = function (city) {
        var level = this.map.getZoom();
        this.map.centerAndZoom(city, level);
    }
    Map.WGS2Baidu = function (pt) {
        //TODO
    }
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
    }
    Map.Google2Baidu = function (pt) {
        this.WGS2Baidu();
    }
    /**
     * get the bounds of the map in json format
     */
    Map.getBounds = function () {
        var bounds = this.map.getBounds();
        var boundsJSON = {
            "w": bounds.getSouthWest().lng,
            "s": bounds.getSouthWest().lat,
            "n": bounds.getNorthEast().lng,
            "e": bounds.getNorthEast().lat,
        };
        return boundsJSON;
    }
    win.HuMap = Map;
})(window, document);