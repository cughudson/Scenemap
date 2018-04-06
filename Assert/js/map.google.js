//most require zepto and baidu map or google map first
//for baidumap
(function (win, doc) {
    var Map = {};
    /**
     * currentMarker['naturescene'] for naturescene marker
     * currentMarker['culturescene'] for culturescene marker
     * currentMarker['hotel'] for hotal marker
     */
    Map.currentMarker = [];

    // in google map pt is an LatLng class
    Map.init = function (id, lat, lng) {
        //0 is represent baidumap
        var mapOpt = {
            center: {'lat': lat, 'lng': lng},
            zoom: 5,
            maptTypeId: google.maps.MapTypeId.ROADMAP,
            mapTypeControl: true,
            mapTypeControlOptions: {
                position: google.maps.ControlPosition.TOP_RIGHT,
                style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
            }
        };
        var map = new google.maps.Map($("#" + id)[0], mapOpt);
        this.map = map;
    };
    Map.loadData = function (param, url, func) {
        this.map.addEventListener("bounds_changed", function () {
            $.post(url, param, function (data, status, xhr) {
                if (status == 'success') {

                } else {
                    //
                }
            })
        });
    };
    /**
     *
     * @param {point} pt the location of the marker
     * @param {path} image the path to the image
     * @param {callback} func the function will invoke when user click the marker
     */
    Map.CreateMarker = function (pts, image, type, id, func) {

        pts.forEach(pt => {

            var Icon = new google.map.Icon();
            Icon.anchor = pt;
            Icon.url = image;
            Icon.size = new google.map.Size(100, 100);
            var MarkerOpt = {
                "anchorPoint": pt,
                "clickable": true,
                "icon": Icon
            };
            var marker = new google.maps.Marker(MarkerOpt);
            marker.type = type;
            marker.id = id;
            marker.setMap(this.map);
            this.currentMarker.push(marker);
            marker.addEventListener("click", function (evt) {
                func();
            })
        });
    };
    Map.FromLatLngToPoint = function (LatLng) {

    };
    Map.FromPointToLatLng = function (LatLng) {

    };
    Map.GetMarker = function (filter) {
        var returnData = [];
        this.currentMarker.forEach(item => {
            if (item.type == filter) {
                returnData.push(item);
            }
        });
        return returnData;
    };
    Map.hideMarker = function (filter) {

        this.currentMarker.forEach(ele => {
            if (ele.type == filter) {
                ele.setVlsiable(false);
            }
        })
    };
    Map.setCenter = function (pt) {
        this.map.setCenter(pt);
    };
    Map.ShowMarker = function (filter) {

        this.currentMarker.forEach(ele => {
            if (ele.type == filter) {
                ele.setVlsiable(true);
            }
        });
    };
    Map.RemoveMarker = function (filter) {
        this.currentMarker.forEach(ele => {
            if (ele.type == filter) {
                var index = this.currentMarker.indexOf(ele);
                this.currentMarker.splice(index, 1);
                ele.setMap(null);
            }
        });
    };
    Map.RemoveAllMarker = function () {
        this.currentMarker.forEach(item => {
            item.setMap(null);
            this.currentMarker = [];
        })
    };
    Map.setCurrentCity = function (city, error) {
        var GeoCoder = new google.maps.GeoCoder();
        var request = new google.maps.GeocoderRequest();
        request.address = city;
        GeoCoder.geocode(request, function (result, staus) {
            if (status == GeocoderStatus.OK) {
                var geo = result.geometry;
                this.map.setCenter(geo.location);
            } else {
                error();
            }
        });
    };
    Map.WGS2Baidu = function (pt) {
        //TODO
    };
    Map.Google2Baidu = function (pt) {
        this.WGS2Baidu();
    };
    Map.GetCityFromPoint = function (pt) {

    };
    /**
     * get the bounds of the map in json format
     *
     * Latitude ranges between -90 and 90 degrees, inclusive.
     *  Values above or below this range will be clamped to the range [-90, 90].
     *  This means that if the value specified is less than -90, it will be set to -90.
     * And if the value is greater than 90, it will be set to 90.
     * Longitude ranges between -180 and 180 degrees, inclusive.
     *  Values above or below this range will be wrapped so that they fall within the range.
     * For example, a value of -190 will be converted to 170.
     *  A value of 190 will be converted to -170.
     * This reflects the fact that longitudes wrap around the globe.
     *
     */
    Map.getBounds = function () {
        var bounds = this.map.getBounds();
        var boundData = {
            "w": bounds.getSouthWest().lng(),
            "s": bounds.getSouthWest().lat(),
            "n": bounds.getNorthEast().lng(),
            "e": bounds.getNorthEast().lat(),
        };
        return boundsData;
    }
})(window, document);