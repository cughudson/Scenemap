(function(win,doc){
    var hudMap = function(map){
        if(instanceof(map)!=""){
            console.log("init the map failed")
        }
        this.map = map;
    };
    
    window.hudMap = hudMap;
}(window, document))