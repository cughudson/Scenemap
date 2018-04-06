'esversion:6';
$(document).ready(
    function () {
        'use strict';
        //editor component
        //require Zepto.js
        // require BMap
        var Component = {};
        Component.QuillViewer = function (id) {
            var config = {
                module: {}
            };
            if (window.Quill == undefined) {
                console.error("quill is not defined");
                return;
            }
            this.quill = new Quill(id, config);
            this.quill.disable(true);
            this.view = function (jsonText) {
                this.quill.setContents(jsonText);
            };
            this.getRawText = function () {
                return this.quill.getText();
            };
            return this;
        };
        Component.quillEditor = function (id, config) {

            // var Font = Quill.import("formats/font")
            var defaultConfig = {
                modules: {
                    toolbar: [
                        [{'header': [1, 2, 3, 4]}, {'font': []}],
                        [{'list': 'ordered'}, {'list': 'bullet'}],
                        ['bold', 'italic', 'underline', 'code-block', {'color': []}],
                        [{'script': 'sub'}, {'script': 'super'}],
                        ['image', 'link']
                    ]
                },
                placeholder: '填写要发布的内容,采用图文形式进行排版',
                theme: 'snow'  // or 'bubble'
            };
            var config = config || defaultConfig;
            const MINHEIGHT = 240;
            const MAXHEIGHT = 1024;

            var quill = new Quill(id, config);
            var dragToolbar = $(id).next();
            var startMouseMove = true;
            var startPosAtY = 0;
            var editorH;
            this.quill = quill;
            dragToolbar.on("mousedown", function (evt) {
                startMouseMove = true;
                startPosAtY = evt.pageY;
                editorH = $(id).height();
            });
            this.quill.on("text-change", function (delta, oldDelta, source) {
                if (quill.getContents().length > 2000) {
                    alert("输入的内容长度不能大于2000");
                }
            });
            $(document).on("mousemove", function (evt) {
                let currentPosAtY;
                let currentEditorH;
                if (startMouseMove) {
                    currentPosAtY = evt.pageY;
                    currentEditorH = ((currentPosAtY - startPosAtY) + editorH) > MAXHEIGHT ? MAXHEIGHT : ((currentPosAtY - startPosAtY) + editorH) < MINHEIGHT ? MINHEIGHT : ((currentPosAtY - startPosAtY) + editorH);
                    $(id).css("height", currentEditorH);
                }
            });
            $(document).on("mouseup", function (evt) {
                startMouseMove = false;
            });
            this.enable = function () {
                this.enable();
            };

            this.viewer = function () {

            };
            this.getContents = function () {
                return this.quill.getContents();
            };
            this.setContents = function (delta) {
                return this.quill.setContents(delta);
            };
            /////////////////////////////////////////////////////////////////////////
            this.AddImageHandler = function (url) {
                var editor = this.quill;
                var toolbar = this.quill.getModule('toolbar');
                toolbar.addHandler('image', function () {
                    uploadImage(url, editor);
                    return true;
                });
            };
            this.getUploadProgress = function (url, success, complete, failed) {

                var Id = setInterval(function () {
                    fetchProgress(url, success, complete, failed)
                }, 50);
                var fetchProgress = function (url, successFunc, completeFunc, failedFunc) {
                    $.get(url, {}, function (respond) {
                        var json = JSON.parse(respond);
                        if (json.status) {
                            var progress = json.progress;
                            if (progress == 100) {
                                clearInterval(Id);
                                completeFunc();
                            } else {
                                successFunc(progress);
                            }
                        }
                    }).fail(function () {
                        failedFunc();
                    })
                }
            };
            var uploadImage = function (url, editor) {

                const input = document.createElement('input');
                input.setAttribute('type', 'file');
                input.multiple = false;
                input.click();

                input.onchange = function () {
                    const file = input.files[0];
                    if (file.type != "image/jpeg") {
                        alert("所选择的图片格式不正确,应上传jpg格式的图片");
                        return;
                    }
                    if (file.size / 1024 > 256) {
                        alert("所选择的图片过大，应上传不大于256kb的图片");
                        return;
                    }
                    saveToServer(url, file, editor);
                };
                var saveToServer = function (url, file, editor) {
                    const fd = new FormData();
                    fd.append('file', file);
                    $.ajax({
                        url: url,
                        data: fd,
                        processData: false,
                        contentType: false,
                        type: 'POST',
                        success: function (respond) {
                            var fileInfo = (JSON.parse(respond));
                            $("#surface").val(fileInfo.fileName);
                            InsertInToEditor(editor, fileInfo.fullPath);
                        },
                        error: function () {
                            alert("上传失败");
                        }
                    })
                };
                var InsertInToEditor = function (editor, filePath) {
                    const range = editor.getSelection();
                    editor.insertEmbed(range.index, 'image', filePath);
                }
            };
            return this;
        };

        Component.PickerMap = function (id, city, level, type) {

            this.id = id;
            var mapCenter;
            var currentPickPos = [];
            var currentMarker = [];
            this.type = type;
            this.city = city;
            this.addressText = null;
            this.address = null;
            this.init = function () {

                if (this.type == "baidu") {
                    if (window.BMap == undefined) {
                        console.error("BMap 尚未定义");
                        return;
                    }
                    this.map = new BMap.Map(this.id);
                    this.map.enableZoom = true;
                    this.map.addControl(new BMap.MapTypeControl({
                        mapTypes: [BMAP_NORMAL_MAP, BMAP_HYBRID_MAP],
                        anchor: BMAP_ANCHOR_TOP_LEFT
                    }));
                    window.map = this.map;
                    this.map.enableScrollWheelZoom();
                    this.map.disableDoubleClickZoom();
                    this.map.centerAndZoom(this.city, level);
                    return this;
                } else {
                    var ele = $("#" + this.id)[0];
                    var GeoCoder = new google.maps.Geocoder();
                    this.map = new google.maps.Map(ele, {
                        center: {lat: 12.45, lng: 36.90},
                        zoom: 8
                    });
                    return this;
                }
            };
            this.addMarker = function (pt) {
                if (currentMarker.length == 1) {
                    this.map.removeOverlay(currentMarker.pop());
                    currentMarker.pop();
                }
                currentMarker.push(this.createMarker(pt));
                this.map.addOverlay(currentMarker[0]);
            };
            this.createMarker = function (pt) {
                let marker = new BMap.Marker(pt);
                return marker;
            };
            this.picker = function () {
                return currentPickPos[0];
            };
            this.value = function () {
                return JSON.stringify(currentPickPos[0]);
            };
            this.value2 = function () {
                return currentPickPos[0].lng + ',' + currentPickPos[0].lat;
            };
            this.enableDbClick = function () {
                var that = this;
                this.map.addEventListener("dblclick", function (evt) {
                    var pixel = new BMap.Pixel(evt.offsetX, evt.offsetY);
                    var point = that.map.pixelToPoint(pixel);
                    if (currentPickPos.length == 1) {
                        currentPickPos.pop();
                    }
                    currentPickPos.push(point);
                    that.addMarker(point);
                    var geoCode = new BMap.Geocoder();

                    geoCode.getLocation(point, function (rs) {
                        var addComp = rs.addressComponents;
                        that.addressText = addComp.province + ", " + addComp.city + ", " + addComp.district + ", " + addComp.street + "," + addComp.streetNumber;
                        that.address = JSON.stringify(addComp);
                    })
                });
            };
        };
        //right panel;
        Component.RightPanel = function (id) {
            var ele = $(id);
            var close = $(".close", ele);
            var cancel = $(".cancel", ele);
            var ok = $(".ok", ele);
            var overlay = $("#overlay");

            ele.Close = function (closeFunc) {
                close.on("click", function (evt) {
                    ele.animate({right: '-200%'}, 500, 'swing', function () {
                        ele.addClass("hidden")
                    });
                    overlay.animate({opacity: 0}, 500, 'swing', function () {
                        overlay.addClass("hidden")
                    });
                    if ($.isFunction(closeFunc)) {
                        closeFunc();
                    } else {
                        console.error("Fatal Error")
                    }
                })
            };
            ele.OK = function (Func) {
                ok.on('click', function (evt) {
                    ele.animate({right: '-200%'}, 500, 'swing', function () {
                        ele.addClass("hidden")
                    });
                    overlay.animate({opacity: 0}, 500, 'swing', function () {
                        overlay.addClass("hidden")
                    });
                    if ($.isFunction(Func)) {
                        Func();
                    } else {
                        console.error("Fatal Error")
                    }
                })
            };
            ele.Cancel = function (cancelFunc) {
                cancel.on('click', function (evt) {
                    ele.animate({right: '-200%'}, 500, 'swing', function () {
                        ele.addClass("hidden")
                    });
                    overlay.animate({opacity: 0}, 500, 'swing', function () {
                        overlay.addClass("hidden")
                    });
                    if ($.isFunction(cancelFunc)) {
                        cancelFunc();
                    } else {
                        console.error("Fatal Error")
                    }
                })
            };
            ele.Open = function () {
                ele.removeClass("hidden");
                ele.animate({right: '0'}, 500, 'swing');
                overlay.removeClass("hidden");
                overlay.animate({opacity: 0.5}, 500, 'swing');
            };
            return ele;
        };
        Component.BaiduStaticMap = function (id, city, level) {
            var id = id.replace("#", "");
            var mapCenter;

            if (!$.isArray(center)) {
                console.log("请输入正确的位置数据格式");
                return;
            }
            this.map = new BMap.Map(id);
            this.map.enableZoom = true;
            this.mapCenter = new BMap.Point(center[0], center[1]);
            this.map.centerAndZoom(new BMap.Point(center[0], center[1]), level);
            this.moveTo = function (pt) {
                this.map.panTo(new BMap.Point(pt[0], pt[1]), 2000);
            };
            this.zoomToPoint = function (pt) {
                if (this.getZoom() == 12) return;
                this.map.centerAndZoom(new BMap.Point(pt[0], pt[1]), 12);
            };
            this.ViewFitTheBounds = function () {

            };
        };
        Component.GoogleStaticMap = function (id, city, level) {
            var id = id.replace("#", "");
            var mapCenter;

            if (!$.isArray(center)) {
                console.log("请输入正确的位置数据格式");
                return;
            }
            this.map = new BMap.Map(id);
            this.map.enableZoom = true;
            this.mapCenter = new BMap.Point(center[0], center[1]);
            this.map.centerAndZoom(new BMap.Point(center[0], center[1]), level);
            this.moveTo = function (pt) {
                this.map.panTo(new BMap.Point(pt[0], pt[1]), 2000);
            };
            this.zoomToPoint = function (pt) {
                if (this.getZoom() == 12) return;
                this.map.centerAndZoom(new BMap.Point(pt[0], pt[1]), 12);
            };
            this.ViewFitTheBounds = function () {

            };
        };
        Component.StickElement = function (selector, stickCls) {
            let ele = $(selector);
            $(window).on("scroll", function (evt) {
                if (document.body.scrollTop != 0) {
                    ele.trigger("ele:stick");
                } else {
                    ele.trigger("ele:normal");
                }
            });
            ele.on("ele:stick", function () {
                if (!ele.hasClass(stickCls)) {
                    ele.addClass(stickCls);
                    console.log("stick");
                }
            });
            ele.on("ele:normal", function () {
                if (ele.hasClass(stickCls)) {
                    ele.removeClass(stickCls);
                }
            });
            return ele;
        };
        Component.SlideMenu = function (selector, triggerEleSelector) {
            let target = $(selector);
            let btn = $(triggerEleSelector);
            const hintClass = "menu-close";
            const headerState = "open";

            btn.on("click", function () {
                if (target.hasClass(hintClass)) {
                    target.removeClass(hintClass);
                    btn.addClass(headerState);
                } else {
                    btn.removeClass(headerState);
                    target.addClass(hintClass);
                }
            });

        };
        Component.Slider = function (prevSelector, nextSelector, nextFunc, preFunc) {

            let prevEle = $(prevSelector);
            let nextEle = $(nextSelector);

            prevEle.on("click", function () {
                //trigger when next btn is click;
                nextFunc();
            });
            nextEle.on("click", function () {
                //trigger when previous btn is click;
                prevFunc();
            });
        };
        window.Component = Component;
    }
);