'esversion:6';
$(document).ready(
    function(){
        'use strict'
        //editor component
        var Component = {};
        Component.quillEditor = function(id,config){
            
            var defaultConfig = {
                modules: {
                    toolbar:true,
                },
                placeholder: '填写要发布的内容',
                theme: 'snow'  // or 'bubble'
            };
            var config = config||defaultConfig;
            const MINHEIGHT = 240;
            const MAXHEIGHT = 1024;

            var quill = new Quill(id, config);
            var dragToolbar = $(id).next();
            var startMouseMove = true;
            var startPosAtY = 0;
            var editorH ;
            dragToolbar.on("mousedown", function(evt){
                startMouseMove = true;
                startPosAtY = evt.pageY;
                editorH = $(id).height();
            });
            $(document).on("mousemove", function(evt){
                let currentPosAtY;
                let currentEditorH;
                if(startMouseMove){
                    currentPosAtY = evt.pageY;
                    currentEditorH = ((currentPosAtY-startPosAtY) + editorH) > MAXHEIGHT?MAXHEIGHT:((currentPosAtY-startPosAtY) + editorH) < MINHEIGHT?MINHEIGHT:((currentPosAtY-startPosAtY) + editorH);
                    $(id).css("height", currentEditorH);
                }
            });
            $(document).on("mouseup", function(evt){
                startMouseMove = false;
            });
            return quill;
        };
        
        Component.PickerMap = function(id, center, level){

            var id = id.replace("#","");
            var map = new BMap.Map(id);
            var mapCenter;
            var currentPickPos = [];
            var currentMarker = [];

            if(!(center instanceof Array)){
                console.log("请输入正确的位置数据格式");
                return;
            }
            map.enableZoom = true;
            mapCenter = new BMap.Point(center[0], center[1]);
            map.enableScrollWheelZoom();
            map.disableDoubleClickZoom()
            map.centerAndZoom(new BMap.Point(center[0], center[1]), level);

            map.addMarker = function(pt){
                if(currentMarker.length == 1){
                    map.removeOverlay(currentMarker.pop());
                    currentMarker.pop();
                }
                currentMarker.push(map.createMarker(pt));
                map.addOverlay(currentMarker[0]);
            };
            map.createMarker = function(pt){
                let marker = new BMap.Marker(pt);
                return marker;
            };
            map.picker = function(){
                return currentPickPos[0];
            };
            map.addEventListener("dblclick", function(evt){
                var pixel =  new BMap.Pixel(evt.offsetX, evt.offsetY);
                var point = map.pixelToPoint(pixel);
                if(currentPickPos.length == 1){
                    currentPickPos.pop();
                }
                currentPickPos.push(point);
                map.addMarker(point);
                $(".data",$("#"+id).next()).html("lng: " + map.picker().lng + ",  lat: " + map.picker().lat);
            });
            return map;
        };
        //right panel;
        Component.RightPanel = function(id){
            var ele = $(id);
            var close = $(".close", ele);
            var cancel = $(".cancel",ele);
            var ok = $(".ok",ele);

            ele.Close = function(func){
                close.on("click", function(ent,func){
                    ele.addClass("close-right");
                    func();
                })
            };
            ele.OK = function(func){
                ok.on('click', function(evt,func){
                    ele.addClass("close-right");
                    return func();
                })
            };
            ele.Cancel = function(func){
                cancel.on('click', function(evt, func){
                    ele.addClass("close-right");
                    func();
                })
            }
            return ele;
        };
        //TimeInput component
        Component.TimeInput = function(id,errCls, normalCls){
            var obj = {};
            var ele = $(id);
            var format = ele.attr("format");
            obj.test = function(format){
                var value = ele.value;
                //\d{4} YYYY
                //(((0[1-9]|(1[0-2])))) MM
                //((0[1-9])|([1-2][0-9])|(3[0-1]))) DD
                var regex =  /\d{4}-(((0[1-9])|(1[0-2])))(-((0[1-9])|([1-2][0-9])|(3[0-1])))/ig;
                return regex.test(value);
            };
            ele.on("keyup", function(){
                if(obj.test){
                    ele.removeCllass(errCls).addClass(normalCls);
                }else{
                    ele.removeCllass(errCls).addClass(normalCls);
                }
            });
            return obj;
        };
        window.Component = Component;
    }
)