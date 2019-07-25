//ajax.js
jQuery(document).ajaxSend(function(event, xhr, settings) {
    function getCookie(name) {
        var reg = new RegExp("(^| )" + name + "(?:=([^;]*))?(;|$)"),
            val = document.cookie.match(reg);
        return val ? (val[2] ? unescape(val[2]) : "") : null;
    }
    if (typeof xhr.setRequestHeader == "function") {
        if (getCookie('csrf_cookie_name')) {
            xhr.setRequestHeader('X-CSRF-TOKEN', getCookie('csrf_cookie_name'));
        }
    }
});

jQuery(document).ajaxSuccess(function(event, xhr, options) {
    try {
        var ret = $.parseJSON(xhr.responseText);
        if (ret.__logs) {
            var logs = ret.__logs;
            for (var i in logs) {
                var log = logs[i];
                if (log instanceof Function) {
                    continue;
                }
                switch (log['type']) {
                    case 'log':
                        console.log("{0} => ".format(log['key']), log['value']);
                        break;

                    case 'info':
                        console.info("{0} => ".format(log['key']), log['value']);
                        break;

                    case 'error':
                        console.error("{0} => ".format(log['key']), log['value']);
                        break;

                    case 'warn':
                        console.warn("{0} => ".format(log['key']), log['value']);
                        break;

                    default:
                        console.log("{0} => ".format(log['key']), log['value']);
                        break;

                }
            }
        }
    } catch (e) {}
});

/**
 * 定义加密函数
 * @param  {[type]} data [形参，需要加密的值]
 * @return {[type]}   [加密后的值]
 */
function encode(data, key) {

    if (key.length != 32) return; //如果key的长度不等于32则返回

    var key_base = key.substring(0, 16);
    var iv_base = key.substring(16, 32);

    var get = function (data) {
        var key_hash = CryptoJS.MD5(key_base);
        key_base = CryptoJS.enc.Utf8.parse(key_base);
        iv_base = CryptoJS.enc.Utf8.parse(iv_base);
        data = CryptoJS.AES.encrypt(data, key_base, {
            iv: iv_base,
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.Pkcs7 // 后台用的是pad.Pkcs5,前台对应为Pkcs7
        });

        data = CryptoJS.enc.Base64.stringify(data.ciphertext);
        data = data.replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
        return data;
    }
    return get(value);
}

function decode(data, key) {

    if (key.length != 32) return;  //如果key的长度不等于32则返回

    var key_base = key.substring(0, 16);
    var iv_base = key.substring(16, 32);
    key_base = CryptoJS.enc.Utf8.parse(key_base);
    iv_base = CryptoJS.enc.Utf8.parse(iv_base);

    var rem = data.length % 4;
    var dd = "";
    for (var i = 0; i < (4 - rem); i++) {
        dd += '='
    }
    data = data.replace(/-/g, '+').replace(/_/g, '/');
    data = data+dd;
    
    data = CryptoJS.AES.decrypt(data, key_base, {
        iv: iv_base,
        mode: CryptoJS.mode.CBC,
        padding: CryptoJS.pad.Pkcs7  
    });

    // 经过CryptoJS解密后，依然是一个对象，将其变成明文就需要按照Utf8格式转为字符串
    data = data.toString(CryptoJS.enc.Utf8);
    return data;
}

if (!String.prototype.format) {
    String.prototype.format = function() {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function(match, number) {
            return typeof args[number] != 'undefined' ?
                args[number] : match;
        });
    };
}

if (!String.prototype.gblen) {
    String.prototype.gblen = function() {
        var len = 0;
        for (var i = 0; i < this.length; i++) {
            if (this.charCodeAt(i) > 127 || this.charCodeAt(i) == 94) {
                len += 2;
            } else {
                len++;
            }
        }
        return len;
    }
}

if (!String.prototype.cutString) {
    String.prototype.cutString = function(max, start) {
        start = start || 0;
        var string = '';
        var len = 0;
        var x = 1;
        for (var i = 0; i < this.length; i++) {
            if (len >= max) {
                break;
            }
            if (this.charCodeAt(i) > 127 || this.charCodeAt(i) == 94) {
                x = 2;
            } else {
                x = 1;
            }
            if (start > 0) {
                start -= x;
            } else {
                len += x;
            }
            (len > 0 && len <= max) && (string += this[i]);
        }
        return string;
    }
}

if (!Date.prototype.getString) {
    Date.prototype.getString = function(day, month, year) {
        day = parseInt(day) || 0;
        month = parseInt(month) || 0;
        year = parseInt(year) || 0;
        //生成新的日期
        var tmp = new Date(this.getFullYear() + year, this.getMonth() + month, this.getDate() + day);
        var y = tmp.getFullYear();
        var m = ((tmp.getMonth() + 1).toString().length == 2) ? tmp.getMonth() + 1 : ("0" + (tmp.getMonth() + 1).toString()); //获取当前月份的日期
        var d = (tmp.getDate().toString().length == 2) ? tmp.getDate() : ("0" + tmp.getDate().toString());
        return y + "-" + m + "-" + d;
    }
}

//写入COOKIES
function setCookie(name, value, expires, path, domain, secure) {

    var exp = new Date();
    expires = arguments[2] || null;
    path = arguments[3] || "/";
    domain = arguments[4] || null;
    secure = arguments[5] || false;
    expires ? exp.setMinutes(exp.getMinutes() + parseInt(expires)) : "";
    document.cookie = name + '=' + escape(value) + (expires ? ';expires=' + exp.toGMTString() : '') + ';path=' + path + (domain ? ';domain=' + domain : '') + (secure ? ';secure' : '');
}

function in_array(id, array) {
    return $.inArray(id, array) >= 0 ? true : false;
}

function array_key_exists(key, array) {
    for (var i in array) {
        if (key == i) {
            return true;
        }
    }
    return false;
}

function array_delete(value, array) {
    array.splice($.inArray(value, array), 1);
    return array;
}

function checkTaggle(checked, name) {
    $('input[type="checkbox"][name="' + name + '"]').prop("checked", checked)
}

function reload() {
    window.location.reload();
}

function back() {
    window.history.go(-1);
}

function getFormArray(dom) {
    var result = {};
    var params = $(dom).serializeArray();
    for (var i in params) {
        var param = params[i];
        result[param['name']] = param['value'];
    }
    return result;
}

function str_repeat(str, n) {
    var list = [];
    while (list.length < n) {
        list.push(str);
    }
    return list.join('');
}

function getCheckBoxByName(name, isInt) {
    if (isInt == undefined) {
        isInt = true;
    }
    var ids = [];
    $('input[type="checkbox"][name="' + name + '"]:checked').each(function() {
        if (isInt) {
            var value = parseInt($(this).val());
        } else {
            var value = $(this).val();
        }
        ids.push(value);
    });
    return ids;
}

function htmlEncode(str) {
    if (typeof str == "string") {
        return str.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, "&#039;").replace(/</g, '&lt;').replace(/>/g, '&gt;');
    } else {
        return str;
    }
}

function htmlDecode(str) {
    if (typeof str == "string") {
        return str.replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
    } else {
        return str;
    }
}

function changeLanguage(lan) {
    setCookie('kali_language', lan, 24 * 60 * 30);
    window.location.reload();
}

//Jquery Coffee
(function($) {
    $.fn.Coffee = function(options) {
        for (var obj in options) {
            var events = options[obj];
            for (evt in events) {
                var func = events[evt];
                $(this).on(evt, obj, func);
            }
        }
    };
})(jQuery);

$(function() {
    $(document).Coffee({
        "input[type=text][maxLen],textarea[type=text][maxLen]": {
            input: function() {
                var max = parseInt($(this).attr('maxLen'));
                if (this.value.gblen() > max) {
                    this.value = this.value.cutString(max);
                }
            }
        },
        "[tips]": {
            mousemove: function(e) {
                if (!$(this).attr("tips")) {
                    return;
                }
                if (this.nodeName == 'SELECT' && $(this).is(':focus')) {
                    return;
                }
                if ($('#tooltipdiv').length == 0) {
                    var content = htmlEncode($(this).attr("tips")).replace(/[\n\r]/g, '<br />');
                    var tooltipdi = '<div id="tooltipdiv" class="txDivTips">' + content + '</div>';
                    $("body").append(tooltipdi);
                    $("#tooltipdiv").show();
                }
                $("#tooltipdiv").css({
                    "top": e.pageY + "px",
                    "left": e.pageX + "px"
                });
            },
            mouseleave: function() {
                $("#tooltipdiv").remove();
            },
            focus: function() {
                if (this.nodeName == 'SELECT' && $('#tooltipdiv').length > 0) {
                    $("#tooltipdiv").remove();
                }
            }
        },
        "input[preg],textarea[preg],select[preg]": {
            "blur change": function() {
                var preg = new RegExp($(this).attr('preg'));
                if (!preg.test($(this).val())) {
                    $(this).removeClass('correct');
                    $(this).addClass('error');
                } else {
                    $(this).removeClass('error');
                    $(this).addClass('correct');
                }
            }
        },
        "input[onEnter]": {
            keypress: function(e) {
                if (e.which == 13) {
                    eval($(this).attr('onEnter'));
                }
            }
        }
    });
});

var str = "Adf";
var qs = (function(a) {
    if (a == '') return {};
    var b = {};
    for (var i = 0; i < a.length; ++i) {
        var p = a[i].split('=');
        if (p.length != 2) continue;
        b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, ' '));
    }
    return b;
})(window.location.search.substr(1).split('&'));

function pageFormSubmit() {
    qs['page_no'] = $('.page_no').val();
    qs['page_size'] = $('.page_size').val();
    location.href = '?' + $.param(qs);
};
//格式化日期
function formatDate(time) {
    var date = new Date(time);
    var year = date.getFullYear();
    var month = date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1);
    var day = date.getDate() < 10 ? '0' + date.getDate() : date.getDate();
    var format = year + '-' + month + '-' + day;
    return format;
}

//获取日期前一天
function getlastDate(time) {
    var date = new Date(time);
    var lastData = new Date(date.getTime() + 24 * 60 * 60 * 1000);
    //formatDate(lastData);
    return lastData;
}
$("body").on("click", ".parent", function() {
    if (this.checked) {
        $(this).parents(".table").find(".child").each(function(i) {
            $(this).prop("checked", true);
        });

        $(this).parents(".widget-box").find(".child").each(function(i) {
            $(this).prop("checked", true);
        });
    } else {
        $(this).parents("table").find(".child").each(function(i) {
            $(this).prop("checked", false);
        });
        $(this).parents(".widget-box").find(".child").each(function(i) {
            $(this).prop("checked", false);
        });
    }
});
//取消反选
$("body").on("click", "input.child", function() {
        var _this = $(this);
        var parent = _this.parents("table");

        if (this.checked) {
            var checkboxs = parent.find('input.child');
            var len = checkboxs.length,
                falg = 0;
            checkboxs.each(function() {
                if (this.checked) {
                    falg++;
                }
            })
            if (len == falg) {
                parent.find("input.parent").prop('checked', true);
            }
        } else {
            parent.find("input.parent").prop('checked', false);
        }
    })
    // select2多选
function selectFn() {

    $("body").find("select[data-plugin=select2]").each(function(index) {
        var url = $(this).data("url");
        var len = $(this).data("minimum-input-length");
        if (len == undefined && url) {
            len = 1;
        } else {
            len = 0;
        }
        var _this = $(this);
        if ($(this).data("url")) { //判断是不是ajax
            $(this).select2({
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term,
                        };
                    },
                    processResults: function(data, params) {
                        return {
                            results: data.results, //itemList
                        };
                    },
                    results: function(data, page) { return data; },
                    cache: true
                },
                minimumInputLength: len,
                placeholder: "请选择"
            })
        } else {
            $(this).select2({
                minimumInputLength: len,
                placeholder: "请选择"
            });
        }
    });

    //验证
    $("select[data-plugin=select2]").change(function() {
        console.log($(this).val())
        var obj = $(this).val();
        var re = $(this).attr("required"); //获取是否要验证
        if (re) {
            if (obj != null) {
                if (obj.length > 0) {
                    $(this).parents(".form-group").removeClass("has-error").find("span.help-block").remove();
                }
            } else {
                $(this).parents(".form-group").removeClass("has-error").find("span.help-block").remove();
                $(this).parents(".form-group").addClass("has-error").children("div").append('<span  class="help-block m-b-none"><i class="fa fa-times-circle"></i>  必填字段</span>');
            }
        }
    });

    //select2 选择排序问题
    $(".select2").on("select2:select", function(evt) {
        var element = evt.params.data.element;
        var $element = $(element);
        $element.detach();
        $(this).append($element);
        $(this).trigger("change");
    });
}

function tokenFn() {
    $('input[data-plugin=tokenfield]').each(function() {
        var option = $(this).attr("data-max-option");
        var tokenArr = [];
        var str = $(this).val();
        var result = str.split(",");
        var re = $(this).attr("required");
        if (str != "") {
            for (var i = 0; i < result.length; i++) {
                tokenArr.push(result[i])
            }
        }

        if (option) {
            $(this).parents(".form-group").addClass("option-wrap");
        }

        $(this).tokenfield().on('tokenfield:createdtoken', function(e) {
            var val = e.attrs.value;
            tokenArr.push(val);
            $(this).parents(".tokenfield ").siblings("input[type=hidden]").val(tokenArr)
            if (re) {
                if (tokenArr.length > 0) {
                    $(this).parents(".form-group").removeClass("has-error").find("span.help-block").remove();
                } else {
                    $(this).parents(".form-group").removeClass("has-error").find("span.help-block").remove();
                    $(this).parents(".form-group").addClass("has-error").children("div").append('<span  class="help-block m-b-none"><i class="fa fa-times-circle"></i>  必填字段</span>')
                }
            }
            $(this).parents(".form-group").find(".validform-wrap").removeClass("has-error").addClass("has-success").find(".Validform_checktip").remove();
        }).on('tokenfield:removedtoken', function(e) {
            var val = e.attrs.value;
            var ind = tokenArr.indexOf(val);
            tokenArr.splice(ind, 1);
            $(this).parents(".tokenfield ").siblings("input[type=hidden]").val(tokenArr);

            if (re) {
                if (tokenArr.length > 0) {
                    $(this).parents(".form-group").removeClass("has-error").find("span.help-block").remove();
                } else {
                    $(this).parents(".form-group").removeClass("has-error").find("span.help-block").remove();
                    $(this).parents(".form-group").addClass("has-error").children("div").append('<span  class="help-block m-b-none"><i class="fa fa-times-circle"></i>  必填字段</span>')
                }
            }
        });
    })
};

var plt = {
    renderPup: function() {
        var content = '<textarea  class="form-control" name="content" placeholder="请输入终断原因"></textarea>' +
            '<div class="form-group" style="margin-top:10px;">' +
            '<label class="col-sm-4 control-label" style="margin-top:8px;text-align:left;"> 禁止登录:</label >' +
            '<div class="col-sm-6" style="padding:0;">' +
            '<div class="radio">' +
            ' <label style="margin-right:10px;">' +
            '<input type="radio" value="1" name="option"> 是' +
            '</label><label>' +
            '<input type="radio" value="2" name="option">' +
            '否</label> </div></div> </div>';
        return content;
    },
    pormpt: function(e) {
        var title = e.target.getAttribute('data-title'),
            href = e.target.getAttribute('data-href');
        if (title == null) {
            title = e.target.parentNode.getAttribute('data-title');
            href = e.target.parentNode.getAttribute('data-href');
        }
        var content = plt.renderPup();
        parent.layer.confirm(content, { title: title },
            function(index, layero) {
                var contentVal = layero.find(".form-control").val();
                var optionVal = layero.find("input[name='option']:checked").val();
                window.location.href = href + '&content=' + contentVal + '&option=' + optionVal;
                parent.layer.close(index);
            },
            function(index) {

            });
    },
    pormptBatch: function(e, cl) {
        var title = e.target.getAttribute('data-title'),
            href = e.target.getAttribute('data-href'),
            errmsg = e.target.getAttribute('data-errmsg');
        if (title == null) {
            title = e.target.parentNode.getAttribute('data-title');
            href = e.target.parentNode.getAttribute('data-href');
            errmsg = e.target.parentNode.getAttribute('data-errmsg');
        }
        if (errmsg == undefined || errmsg == null) {
            errmsg = "请先选择"
        }
        $("[data-href='" + href + "']").parents("form").attr("action", href);
        var valArr = new Array;
        var k = 0;
        $("." + cl).each(function(i) {
            if (this.checked) {
                valArr[k] = $(this).val();
                k++;
            }
        });
        if (cl == undefined || valArr.length > 0) {
            var content = plt.renderPup();
            parent.layer.confirm(content, { title: title },
                function(index, layero) {

                    var contentVal = layero.find(".form-control").val();
                    var optionVal = layero.find("input[name='option']:checked").val();
                    var textarea = '<input type="hidden" class="hidden"  value="' + contentVal + '" name="content">';
                    var input = '<input type="hidden" class="hidden"  value="' + optionVal + '" name="option">';
                    $("[data-href='" + href + "']").parents("form").append(textarea);
                    $("[data-href='" + href + "']").parents("form").append(input);
                    $("[data-href='" + href + "']").parents("form").submit();
                    parent.layer.close(index);
                    $("[data-href='" + href + "']").parents("form").find(".hidden").remove();
                    parent.layer.close(index);
                },
                function(index) {

                });
        } else {
            parent.layer.alert(errmsg, { shadeClose: true });
            return;
        }
    },
    confirmAction: function(e) {
        var title = e.target.getAttribute('data-title'),
            href = e.target.getAttribute('data-href'),
            tipmsg = e.target.getAttribute('data-tipmsg');
        if (title == null) {
            title = e.target.parentNode.getAttribute('data-title');
            href = e.target.parentNode.getAttribute('data-href');
            tipmsg = e.target.parentNode.getAttribute('data-tipmsg');
        }

        if (tipmsg == undefined || tipmsg == null) {
            tipmsg = "确认删除"
        }
        parent.layer.confirm(tipmsg, { icon: 3, title: title },
            function(index) {
                window.location.href = href;
                parent.layer.close(index);
            },
            function(index) {

            });
    },
    subform: function(e, cl) {
        var title = e.target.getAttribute('data-title'),
            href = e.target.getAttribute('data-href'),
            tipmsg = e.target.getAttribute('data-tipmsg'),
            errmsg = e.target.getAttribute('data-errmsg');
        if (title == null) {
            title = e.target.parentNode.getAttribute('data-title');
            href = e.target.parentNode.getAttribute('data-href');
            tipmsg = e.target.parentNode.getAttribute('data-tipmsg');
            errmsg = e.target.parentNode.getAttribute('data-errmsg');
        }
        if (tipmsg == undefined || tipmsg == null) {
            tipmsg = "确认修改"
        }
        if (errmsg == undefined || errmsg == null) {
            errmsg = "请先选择"
        }
        $("[data-href='" + href + "']").parents("form").attr("action", href);
        var  valArr  =  new  Array;
        var k = 0;
        $("." + cl).each(function(i) {
            if (this.checked) {
                valArr[k]  =  $(this).val();
                k++;
            }
        });
        if (cl == undefined || valArr.length > 0) {
            parent.layer.confirm(tipmsg, { icon: 3, title: title },
                function(index) {
                    $("[data-href='" + href + "']").parents("form").submit();

                    parent.layer.close(index);
                },
                function(index) {

                });
        } else {
            parent.layer.alert(errmsg, { shadeClose: true });
            return;
        }
    },
    //弹出用户基本信息
    openIframe: function(e) {
        var title = e.target.getAttribute('data-title'),
            href = e.target.getAttribute('data-href');
        parent.layer.open({
            type: 2,
            title: decodeURI(title),
            shade: 0.3,
            maxmin: true,
            shadeClose: true,
            area: ['600px', '500px'],
            content: href
        });

    },
    getDateEndMonth: function(id) { //日期控件，截至月份
        //日期控件
        $("#" + id).datetimepicker({
            format: 'yyyy-mm',
            language: 'zh-CN',
            pickDate: true,
            pickTime: false,
            minView: 'year',
            startView: 3,
            autoclose: true
        });

        function getNowDate(id) { //默认获取当前日期
            var today = new Date();
            var nowdate = (today.getFullYear()) + "-" + (today.getMonth() + 1) + "-" + today.getDate();
            //对日期格式进行处理
            var date = new Date(nowdate);
            var mon = date.getMonth() + 1;
            var mydate = date.getFullYear() + "-" + (mon < 10 ? "0" + mon : mon);
            $("#" + id).attr("placeholder", mydate)
        }
    }
}

function subform(url) {
    $("#myform").attr('action', url);
    $("#myform").submit();
}

$(function() {
    //权限管理
    function purviewFn() {
        if ("undefined" != typeof MenuData) {
            function getData(index) { //分步循环
                if (MenuData[index].children) {
                    $("#side-menu").empty();
                    var sideStr = "";
                    var arr = MenuData[index].children;

                    function render(arr, level) {
                        var level = level || 0;
                        level++;
                        var i = 0,
                            len = arr.length;
                        if (level == 1) {
                            var str = '<ul class="checkbox-wrap checkbox-item">'
                        } else {
                            str = '<ul class="item-second-level col-sm-10">'
                        }
                        for (; i < len; i++) {
                            if (arr[i].children && arr[i].children.length > 0) {
                                str += '<li class="nav' + arr[i].id + ' block"><label class="has-child pull-left m-r-sm a"  data-index="' + arr[i].id + '"><input type="checkbox" name="" class="checkall tcheckall" /> ' + arr[i].name + '</label>';
                                str += render(arr[i].children, level);
                            } else {
                                str += '<li class="nav' + arr[i].id + ' "><label class="m-r" data-index="' + arr[i].id + '"><input type="checkbox" name="purviews[]" value="' + arr[i].ct + '-' + arr[i].ac + '" class="check"/> ' + arr[i].name + '</label>';
                            }

                            str += '</li>';
                        }

                        if (level == 1) {
                            str += '</ul>';
                        } else {
                            str += '</ul><div class="hr-line-dashed"></div>';
                        }

                        return str;
                    }
                    $("body").find(".form-group-item").eq(index).children(".col-sm-10").html(render(arr))
                } else {
                    return false
                }
            }

            var str = "";
            for (var i = 0; i < MenuData.length; i++) {
                str += '<div class="form-group form-group-item" data-id="' + MenuData[i].id + '"><label class="col-sm-2 control-label"><input type="checkbox" name="" class="checkall pcheckall" style="margin-right:5px"/>' + MenuData[i].name + ':</label><div class="col-sm-10"></div></div>'
            }
            $("#checkbox-group").html(str);

            $("body").find(".form-group-item").each(function(i) {
                getData(i)
            })
            $("body").on("click", ".checkall", function() {
                if (this.checked) {
                    $(this).parent().siblings().find("input").each(function(i) {
                        $(this).prop("checked", true);
                    });
                } else {
                    $(this).parent().siblings().find("input").each(function(i) {
                        $(this).prop("checked", false);
                    });
                }

            });

            $("body").on("click", ".checkbox-item input", function() {
                var len = $(this).closest("ul").find(".check").length;
                var plen = $(this).parents(".form-group-item").find(".check").length,
                    tplen = $(this).closest("ul").parent("li").parent("ul").find(".check").length;
                var flag = 0,
                    pflag = 0,
                    tpflag = 0;
                $(this).closest("ul").find(".check").each(function() {
                    if (this.checked) {
                        flag++;
                    }
                })
                $(this).closest("ul").parent("li").parent("ul").find(".check").each(function() {
                    if (this.checked) {
                        tpflag++;
                    }
                })
                $(this).parents(".form-group-item").find(".check").each(function() {
                        if (this.checked) {
                            pflag++;
                        }
                    })
                    //一级
                if (flag == len) {
                    $(this).closest("ul").siblings("label").find("input").prop("checked", true);
                } else {
                    $(this).closest("ul").siblings("label").find("input").prop("checked", false);
                }
                //top级
                if (pflag == plen) {
                    $(this).parents(".form-group-item").find(".control-label input").prop("checked", true);
                } else {
                    $(this).parents(".form-group-item").find(".control-label input").prop("checked", false);
                }
                //二级
                if (tpflag == tplen) {
                    $(this).closest("ul").parent("li").parent("ul").siblings("label").find("input").prop("checked", true);
                } else {
                    $(this).closest("ul").parent("li").parent("ul").siblings("label").find("input").prop("checked", false);
                }

            })

            if ("undefined" != typeof MenuDataCheck) {
                var checkArr = MenuDataCheck.split(",");
                $("#checkbox-group").find("input").each(function() {
                    var val = $(this).val();
                    if (checkArr.indexOf(val) != -1) {
                        $(this).prop("checked", true);
                    }
                })


                //父级全选
                var k = 0;
                $("#checkbox-group").find(".pcheckall").each(function() {
                    k = 0;
                    var pdiv = $(this).parent("label").siblings("div");
                    var len = $(this).parent("label").siblings("div").find(".check").length;

                    pdiv.find(".check").each(function() {
                        if (this.checked) {
                            k++;
                        }
                    })
                    if (k == len) {
                        $(this).prop("checked", true);
                    } else {
                        $(this).prop("checked", false);
                    }

                })

                var b = 0;
                $("#checkbox-group").find(".tcheckall").each(function() {
                    b = 0;
                    var pdiv = $(this).parent("label").siblings("ul");
                    var len = $(this).parent("label").siblings("ul").find(".check").length;

                    pdiv.find(".check").each(function() {
                        if (this.checked) {
                            b++;
                        }
                    })
                    if (b == len) {
                        $(this).prop("checked", true);
                    } else {
                        $(this).prop("checked", false);
                    }

                })
            }
        }
    }

    selectFn(); //多选调用
    tokenFn();
    purviewFn(); //权限管理调用


    if ($.fn.datetimepicker) {

        $("input[data-plugin=start]").datetimepicker({ //开始日期
            format: 'yyyy-mm-dd',
            language: 'zh-CN',
            pickDate: true,
            pickTime: false,
            minView: 'month',
            startView: 2,
            autoclose: true,
            clearBtn: true, //添加清除按钮，可选值：true/false
        }).on('changeDate', function(ev) {

            var starttime = $(this).val(); //获取选择的值
            //var lastData = formatDate(getlastDate(starttime)); //格式化日期，并获取后一天
            var that = this;
            var endDom = $(that).parents(".input-daterange").find("input[data-plugin=end]"); //获取结束日期dom
            var endDateVal = endDom.val();

            function setEndDate(starttime) {
                //endDom.datetimepicker('setStartDate', lastData);
                endDom.val(starttime);
            }
            if (Date.parse(starttime) >= Date.parse(endDateVal) || endDateVal == "") { //判断结束日期是否大于开始日期
                setEndDate(starttime);
            }



        });

        $("input[data-plugin=end]").datetimepicker({
            format: 'yyyy-mm-dd',
            language: 'zh-CN',
            pickDate: true,
            pickTime: false,
            minView: 'month',
            startView: 2,
            autoclose: true,
            clearBtn: true, //添加清除按钮，可选值：true/false
        }).on('changeDate', function(ev) {


            var endDateVal = $(this).val(); //结束日期
            var startDom = $(this).parents(".input-daterange").find("input[data-plugin='start']");
            var startTime = startDom.val(); //开始日期
            if (Date.parse(startTime) >= Date.parse(endDateVal) || startTime == "") { //判断结束日期是否大于开始日期
                startDom.val(endDateVal);
            }
        });
    }


    $(document).on("click", function() {
        if (parent.closeDrop != undefined) {
            parent.closeDrop(this, "child");
        }
    })
})
