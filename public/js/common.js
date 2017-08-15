$(document).ready(
    function(){
        //editor component
        // var quill = new Quill('#rich-editor', {
        //     modules: {
        //         toolbar: [
        //         [{ header: [1, 2, false] }],
        //         ['bold', 'italic', 'underline'],
        //         ['image', 'code-block']
        //         ]
        //     },
        //     placeholder: 'Compose an epic...',
        //     theme: 'snow'  // or 'bubble'
        // });
        var map = new BMap.Map("map");
        var point = new BMap.Point(116.404, 39.915);
        map.centerAndZoom(point,12);

        //right panel;
        var rightPanel = function(id){
            var ele = $(id);
            var close = $(".close", ele);
            var cancel = $(".cancel",ele);
            var ok = $(".ok",ele);

            ele.Close = function(func){
                close.on("click", function(){
                    ele.addClass("close-right");
                    func();
                })
            };
            ele.OK = function(func){
                ok.on('click', function(){
                    ele.addClass("close-right");
                    func();
                })
            };
            ele.Cancel = function(func){
                cancel.on('click', function(){
                    ele.addClass("close-right");
                    func();
                })
            }
            return ele;
        }
        window.rightPanel = rightPanel;
    }
)