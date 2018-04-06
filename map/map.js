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

    Map.init = function (id, city) {
        //0 is represent baidumap
        var map = new BMap.Map(id);
        map.centerAndZoom(city);
        map.addControl(new BMap.MapTypeControl(
            {
                mapTypes: [
                    BMAP_NORMAL_MAP,
                    BMAP_HYBRID_MAP
                ]
            }
        ));
        this.map = map;
    };
    Map.loadData = function (param, url, func) {
        this.addEventListener("moveend", function () {
            $.map.post(url, param, function (data, status, xhr) {
                if (status == 'success') {

                } else {
                    //
                }
            })
        });
        this.map.addEventListener("zoomend", function () {
            $.post(url, param, function (data, status, xhr) {
                if (status == 'success') {

                } else {

                }
            })
        });
        this.map.addEventListener("dragend", function () {
            $.post(url, param, function (data, status, xhr) {
                if (status == 'success') {

                }
            })
        })
    };
    /**
     *
     * @param {point} pt the location of the marker
     * @param {path} image the path to the image
     * @param {callback} func the function will invoke when user click the marker
     */
    Map.CreateMarker = function (pts, image, func, type, id) {

        pts.forEach(pt => {

            var Icon = new BMap.Icon(image, new BMap.Size(100, 100));
            var marker = new BMap.Marker(pt, {icon: Icon});
            marker.type = type;
            marker.id = id;
            this.map.addOverlay(marker);
            marker.addEventListener("click", function (evt) {
                func();
            })
        });
    };
    Map.GetMarker = function (filter) {

        var EleArr = this.map.getOverlay();
        var returnData = [];

        EleArr.forEach(ele => {
            if (ele.type == filter) {
                returnData.push(ele);
            }
        });
        return returnData;
    };
    Map.hideMarker = function (filter) {
        var EleArr = this.map.getOverlay();
        EleArr.forEach(ele => {
            if (ele.type == filter) {
                ele.style.display = "none";
            }
        })
    };
    Map.ShowMarker = function (filter) {
        var EleArr = this.map.getOverlay();
        EleArr.forEach(ele => {
            if (ele.type == filter) {
                ele.style.display = "block";
            }
        });
    };
    Map.RemoveMarker = function (filter) {
        var EleArr = this.map.getOverlay();
        EleArr.forEach(ele => {
            if (ele.type == filter) {
                this.map.removeOverlay(ele);
            }
        });
    };
    Map.RemoveAllMarker = function () {
        this.map.clearOverlay();
    };
    Map.setCurrentCity = function (city) {
        this.map.setCurrentCity(city);
    };
    Map.WGS2Baidu = function (pt) {
        //TODO
    };
    Map.GetCityFromPoint = function (pt, func) {
        var geoCoder = new Map.GeoCoder();
        geoCoder.getLocation(pt, function (result) {
            return result.addressComponents;
        })
    };
    Map.Google2Baidu = function (pt) {
        this.WGS2Baidu();
    };
    /**
     * get the bounds of the map in json format
     */
    Map.getBounds = function () {
        map.getBounds();
        var bounds = {
            "w": bounds.getSouthWest().lng,
            "s": bounds.getSouthWest().lat,
            "n": bounds.getNorthEast().lng,
            "e": bounds.getNorthEast().lat,
        };
        return bounds;
    }
})(window, document);