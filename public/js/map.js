(function(win,doc){

    //const value
    const NATURE = 0;
    const CULTURE = 1;
    const HOTEL = 2;
    //url
    const NATUREURL = "";
    const CULTUREURL = "";
    const HOTELURL = "";

    const MARKERTEMPLATE = "";

    var item = function(id, geoCoordinate, type,thumnail, address,name){
        this.id = id;
        this.geoCoordinate = geoCoordinate;
        this.type = type;
        this.thumnail = thumnail;
        this.address = address;
        this.name = name;
        return this;
    };

    var hudMap = function(map){
        if(a instanceof BMap){
            console.log("init the map failed")
        }
        this.map = BMap;
        this.items = [];
        this.add  =function(item){
            var id = item.id;
            var signal = false;
            for(var i = 0; i < this.items.length; i++){
                if(this.items[i].id == id){
                    signal = true;
                    console.warn("添加失败，集合中已经有id为"+id+"的标签");
                    return false;
                }
            }
            if(!signal){
                this.items.push(item);
                return true;
            }
        };
        this.remove = function(item){
            var id = item.id;
            var signal = false;
            for(var i = 0; i < this.items.length; i++){
                if(this.items[i].id == id){
                    signal = true;
                    this.items.pop(this.item[i]);
                    return true;
                }
            }
            if(!signal)
                console.warn("删除失败，集合中不存在id为"+id+"的标签");
                return false;
            }
        };
        this.findItem = function(item){
            var id = item.id;
            var signal = false;
            for(var i = 0; i < this.items.length; i++){
                if(this.items[i].id == id){
                    signal = true;
                    return this.items[i];
                }
            }
            if(!signal){
                console.warn("查找失败，集合中不存在id为"+id+"的标签");
                return false;
            }

        };
        this.addOverlay = function(item){

        };
        this.removeOverlay = function(type){
            switch(type){
                case NATURE:
                break;
                case CULTURE:
                break;
                case HOTEL:
                break;
                default:
            }
        };
    window.hudMap = hudMap;
}(window, document))