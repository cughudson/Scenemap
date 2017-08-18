(function(win,doc){

    //const value
    const NATURE = "NATURE";
    const CULTURE = "CULTURE";
    const HOTEL = "HOTEL";
    //url
    const NATUREICONURL = "";
    const CULTUREICONURL = "";
    const HOTELICONURL = "";

    const HOTELTEMPLATE = "";
    const SCENETEMPLATE = "<div class='window'><div class='thumnail-wrapper'><img src={{href}}></div><h4>{{title}}</h4><p><span class'name'>地址：</span><span>{{address}}</span></p></div>"

    var item = function(id, info){
        this.id = id;
        this.info = info;
        return this;
    };
    var CustomMap = function(id){
        //
        //BMap is baidu map
        //
        this.id = id.replace("#","");
        //holding the markers that has not adding to the map
        // map holding the marker that is active;
        //markers 始终保持不变
        this.markers = [];
        this.item = [];
        this.create = function(center,level){
            try{
                this.tempMap = new BMap.Map(this.id);
                tempMap.enableScrollWheelZoom();
                tempMap.disableDoubleClickZoom();
                tempMap.centerAndZoom(new BMap.Point(center.lng, center.lat), level);
                return this;
            }catch(e){
                console.error(e.message);
                return false;
            }
        };
        this.create = function(city,level){
            try{
                this.tempMap = new BMap.Map(this.id);
                tempMap.enableScrollWheelZoom();
                tempMap.disableDoubleClickZoom();
                tempMap.centerAndZoom(city, level);

                return this;
            }catch(e){
                console.error(e.message);
                return false;
            }
        };
        this.createMarker = function(url, item){
            var opt = {
                icon:new BMap.Icon(url,this.map.Size(32,32))
            };
            let marker = new BMap.Marker(item.point,opt);
            marker.id = item.info.id;
            marker.type = item.type;
            marker.item = item;
            return marker;
        };
        this.constructInfoWindow = function(marker){

        }
        this.getDataById = function(id){

        };
        this.removeMarker = function(marker){

            if(arg instanceof Array){
                for(var i = 0; i < arg.length; i++){
                    this.map.removeOverlay(arg[i]);
                }
            }else{
                this.map.removeOverlay(arg);
            }
        };
        this.findMarker = function(marker){
            var id = marker.id;
            var signal = false;
            for(var i = 0; i < this.markers.length; i++){
                if(this.markers[i].id == id){
                    signal = true;
                    return this.markers[i];
                }
            }
            if(!signal){
                console.warn("查找失败，集合中不存在id为"+id+"的标签");
                return false;
            }

        };
        this.addMarker = function(arg){
            if(arg instanceof Array){
                for(var i = 0; i < arg.length; i++){
                    this.map.addOverlay(arg[i]);
                }
            }else{
                this.map.addOverlay(arg);
            }
        };
        this.filter = function(type){

            var tempArr = [];
            for(var i = 0; i < this.markers.length; i++){
                let tempMarker = this.markers[i];
                if(tempMarker.type == type){
                    tempArr.push(tempMarker[i]);
                }
            }
            return tempArr;
        };
    }
    window.CustomMap = CustomMap;
}(window, document))