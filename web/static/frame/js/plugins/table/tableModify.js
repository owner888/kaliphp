/* 
 * 说明1:固定行th上加data-type="fixed"
 * 说明2:分页tr加class='ctrl'
 * 说明3:td,th是否显示增加属性 data-show="yes/no" yes-显示，no-隐藏
 */
;
(function() {
    $("[data-toggle='popover']").popover({
        "trigger": "hover"
    });
    var selectArrDe = [];

    (function bindEvent() {
        /*  //全选
         $(document).on("change", '.checkall input', function(e) {
             var _this = $(this);
             Metheds.checkAll(_this, e.target);
         }) */
        //左侧删除
        $(document).on("click", ".fa-close", function() {
            var _this = $(this);
            var id = _this.parents("li").data("id");
            _this.parents("li").remove();
            Metheds.arrRemove(selectArrDe, id);
            $("#selector").find("input[data-id=" + id + "]").prop('checked', false).trigger('change');
        })

        //单选
        $(document).on("change", ".check input", function(e) {
            var _this = $(this),
                id = _this.val(),
                name = _this.data("name");
            if (this.checked) {
                Metheds.objToArr(selectArrDe, name, id);
                //判断是否全选
                var checkboxs = _this.closest("li").siblings("li.check").find('[type=checkbox]');
                var len = checkboxs.length,
                    falg = 0;
                checkboxs.each(function() {
                    if (this.checked) {
                        falg++;
                    }
                })
                if (len == falg) {
                    _this.closest("li").siblings("li.checkall").find("input").prop('checked', true);
                }
            } else {
                Metheds.arrRemove(selectArrDe, id);
                _this.closest("li").siblings(".checkall").find("input").prop('checked', false);
            }
            Metheds.leftCreate(this, selectArrDe);
            console.log(selectArrDe)
        })

        //搜索
        $(document).on("input", ".search input", function(e) {
            var _this = $(this),
                key = $.trim(_this.val());
            if (key.length > 0) {
                $(this).siblings(".fa-times-circle").show();
                $("#selector").find("li").find("label").each(function() {
                    var text = $.trim($(this).text().toLowerCase());
                    if (text.indexOf(key) == -1) {
                        $(this).parent("li").hide();
                    } else {
                        $(this).parent("li").show();
                    }
                })
            } else {
                $("#selector").find("li").show();
                $(this).siblings(".fa-times-circle").hide();
            }
        })
    })();


    var Metheds = {
        openIfram: function() {
            //生成模版
            $(".selector-wrap").remove();
            selectArrDe = [];
            Metheds.model();

            layer.open({
                type: 1,
                title: "自定义列表字段",
                shade: 0.3,
                maxmin: true,
                shadeClose: true,
                area: ['900px', '560px'],
                content: $(".selector-wrap"),
                btn: ['保存', '关闭'],
                btnAlign: 'center',
                yes: function(index) {
                    var indArr = [];
                    $.each(selectArrDe, function(i, item) {
                        indArr.push(item.id);
                    })
                    console.log(indArr.sort())
                    Metheds.hideItem(indArr.sort());
                    layer.closeAll();
                },
                success: function() {
                    Metheds.tplLeft();
                    Metheds.defaultRigth();
                    var btn = $("body").find('.layui-layer-btn');
                    btn.css({ 'text-align': 'center', "line-height": "40px" });
                    btn.find("a").css({ "line-height": "32px", "height": "32px" });
                }
            });
        },
        model: function() {
            var tpl = '<div class="container-fluid  selector-wrap" >' +
                '<div class="row">' +
                '<div class="col-sm-6">' +
                '<div class="origin-box">' +
                '<div class="">' +
                '<h5 class="stitle">请选择您想显示的列表字段</h5>' +
                '</div>' +
                '<div class="selector-con">' +
                '<ul id="selector"> </ul>' +
                '</div></div> </div>' +
                '<div class="arrow-icon fa fa-arrows-h"></div><div class = "col-sm-6">' +
                '<div class=" selected-box">' +
                '<div class = "">' +
                '<h5 class = "stitle" > 已选择 (<span class="num"></span>/<span class="total"></span>)</h5> </div>' +
                '<div class = "selector-right selector-con">' +
                '<ul>' +
                '</ul> </div> </div> </div> </div> </div>';
            $("body").append(tpl);
        },
        tplLeft: function() {
            var data = Metheds.getDate();
            var str = '<li class="search"><input type="text" placeholder="请输入关键字" /><span class="fa fa-times-circle"></span><span class="fa fa-search"></span></li>';

            for (var i = 0; i < data.length; i++) {
                if (data[i].fixed != undefined) {
                    str += '<li class="check"><label><input type="checkbox" checked="checked" disabled data-fixed="fixed" data-show="' + data[i].show + '" data-name="' + data[i].name + '" data-id="' + data[i].id + '" value="' + data[i].id + '">' + data[i].name + ' </label></li>';
                } else if (data[i].show == 'yes') {
                    str += '<li class="check"><label><input type="checkbox" checked="checked" data-show="' + data[i].show + '" data-name="' + data[i].name + '" data-id="' + data[i].id + '" value="' + data[i].id + '">' + data[i].name + ' </label></li>';
                } else {
                    str += '<li class="check"><label><input type="checkbox" data-show="' + data[i].show + '" data-name="' + data[i].name + '" data-id="' + data[i].id + '" value="' + data[i].id + '">' + data[i].name + ' </label></li>';
                }
            }
            $("#selector").html(str);
        },
        getDate: function() {
            var data = [];
            $(".table").find("thead th").each(function(i) {
                var name = $.trim($(this).text());
                var fixed = $(this).data("type");
                var show = $(this).attr("data-show");
                if (name == "") { name = "操作" }
                Metheds.objToArr(data, name, i, fixed, show);
            })
            $(".total").html(data.length);
            return data;
        },
        objToArr: function(arr, name, id, fixed, show) {
            var flag = false;
            var obj = {};
            obj.id = id;
            obj.name = name;
            obj.fixed = fixed;
            obj.show = show;
            for (var key in arr) {
                if (arr[key].id == id) {
                    flag = true;
                    break;
                }
            }
            if (!flag) {
                arr.push(obj);
            }
        },
        /* checkAll: function(_this, e) {
            var _this = _this;
            var checkboxs = _this.closest("li").siblings("li.check").find('[type=checkbox]');

            if (e.checked) {
                checkboxs.each(function(i) {
                    var pType = $(this).data("fixed");
                    if (pType == undefined) {
                        if (!this.checked) {
                            $(this).prop('checked', true);
                            var name = $(this).data("name");
                            var id = $(this).data("id");
                            Metheds.objToArr(selectArrDe, name, id);
                        }
                    } else {
                        $(this).prop('checked', true);
                        var name = $(this).data("name");
                        var id = $(this).data("id");
                        Metheds.objToArr(selectArrDe, name, id, pType);
                    }
                })
            } else {
                checkboxs.each(function() {
                    var pType = $(this).data("fixed");
                    if (pType == undefined) {
                        var id = $(this).val();
                        if (this.checked) {
                            $(this).prop('checked', false);
                            Metheds.arrRemove(selectArrDe, id);
                        }
                    }
                })
            }
            console.log(selectArrDe)
            Metheds.leftCreate(this, selectArrDe);
        }, */
        arrRemove: function(arr, id) {
            var index = -1;
            arr.forEach(function(item, i) {
                if (item.id == id) index = i;
            });
            if (index != -1)
                arr.splice(index, 1)

        },
        leftCreate: function(e, arr) {
            var _this = $(e);
            var leftstr = "";
            $.each(arr, function(i, item) {
                if (item.fixed == undefined) {
                    leftstr += '<li class="left-item"  data-name="' + item.name + '" data-id="' + item.id + '"><span >' + item.name + '</span> <i class="fa fa-close"></i></li>'
                } else {
                    leftstr += '<li class="left-item"  data-name="' + item.name + '" data-id="' + item.id + '"><span >' + item.name + '</span> </li>'
                }

            })
            $(".num").html(arr.length);
            $(".selector-right ul").html(leftstr);
        },
        defaultRigth: function() {
            var data = Metheds.getDate();
            var leftstr = "";
            var len = 0;
            $.each(data, function(i, item) {
                if (item.fixed != undefined) {
                    len++;
                    leftstr += '<li class="left-item"  data-name="' + item.name + '" data-id="' + item.id + '"><span >' + item.name + '</span> </li>';
                    selectArrDe.push(item);
                } else if (item.show == "yes") {
                    len++;
                    leftstr += '<li class="left-item"  data-name="' + item.name + '" data-id="' + item.id + '"><span >' + item.name + '</span><i class="fa fa-close"></i> </li>';
                    selectArrDe.push(item);
                }
            })
            $(".num").html(len);
            //console.log(len)
            $(".selector-right ul").html(leftstr);
        },
        hideItem: function(arr) {
            $(".table thead th").hide();
            $(".table thead th").attr("data-show", "no");
            for (var i = 0; i < arr.length; i++) {
                $(".table thead th").eq(arr[i]).show();
                $(".table thead th").eq(arr[i]).attr("data-show", "yes");
            }

            $(".table tbody").find("tr").each(function() {
                if (!$(this).hasClass("ctrl")) {
                    $(this).find("td").hide();
                    $(this).find("td").attr("data-show", "no");
                    for (var i = 0; i < arr.length; i++) {
                        $(this).find("td").eq(arr[i]).show();
                        $(this).find("td").eq(arr[i]).attr("data-show", "yes");
                    }
                }
            })
        }
    }
    window.Metheds = Metheds;
})()