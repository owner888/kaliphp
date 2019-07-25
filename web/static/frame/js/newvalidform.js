$(function() {
    //验证------------------------------------------start-------------------

    //多选一自定义方法
    var checkoneFn = function(gets, obj, curform, regxp) {
            /*参数gets是获取到的表单元素值，
              obj为当前表单元素，
              curform为当前验证的表单，
              regxp为内置的一些正则表达式的引用。*/
            var flag = 0;
            var len = curform.find(".checkone").length;
            curform.find(".checkone").each(function() {
                if ($(this).val().length == 0) {
                    flag++
                }
            })
            if (flag == len) {
                return false;
            } else {
                $(".checkone").siblings("span").remove();
                $(".checkone").parent("div").removeClass("has-error").addClass("has-success");
                $(".checkone").parents(".form-group").find(".control-label").css("color", "#1ab394");
                return true;

            }
        }
        //密码必须包含大小写字母，数字
    var passwordFn = function(gets, obj, curform, regxp) {
        var lv = 0;
        var val = $(obj).val();
        if (val.match(/[A-Z]/g)) {
            lv++;
        }
        if (val.match(/[a-z]/g)) {
            lv++;
        }
        if (val.match(/[0-9]/g)) {
            lv++;
        }
        if (val.length >= 6 && val.length <= 18) {
            lv++
        }
        if (lv < 4) {
            return false;
        } else {
            return true;
        }
    }


    //身份证
    var IdCard = function(gets, obj, curform, regxp) {
        var reg = /^((\d{18})|([0-9x]{18})|([0-9X]{18}))$/;
        if (reg.test($(obj).val())) {
            return true;
        } else {
            return false
        }
    }

    //护照
    var passCard = function(gets, obj, curform, regxp) {
        var reg = /^1[45][0-9]{7}|([P|p|S|s]\d{7})|([S|s|G|g]\d{8})|([Gg|Tt|Ss|Ll|Qq|Dd|Aa|Ff]\d{8})|([H|h|M|m]\d{8,10})$/;
        if (reg.test($(obj).val())) {
            return true;
        } else {
            return false;
        }
    }

    //银行卡号
    function luhmCheck(bankno) {
        var lastNum = bankno.substr(bankno.length - 1, 1); //取出最后一位（与luhm进行比较）
        var first15Num = bankno.substr(0, bankno.length - 1); //前15或18位
        var newArr = new Array();
        for (var i = first15Num.length - 1; i > -1; i--) {
            //前15或18位倒序存进数组
            newArr.push(first15Num.substr(i, 1));

        }
        var arrJiShu = new Array(); //奇数位*2的积 <9
        var arrJiShu2 = new Array(); //奇数位*2的积 >9

        var arrOuShu = new Array(); //偶数位数组
        for (var j = 0; j < newArr.length; j++) {
            if ((j + 1) % 2 == 1) {
                if (parseInt(newArr[j]) * 2 < 9)
                    arrJiShu.push(parseInt(newArr[j]) * 2);
                else
                    arrJiShu2.push(parseInt(newArr[j]) * 2);
            } else //偶数位
                arrOuShu.push(newArr[j]);
        }
        var jishu_child1 = new Array(); //奇数位*2 >9 的分割之后的数组个位数
        var jishu_child2 = new Array(); //奇数位*2 >9 的分割之后的数组十位数
        for (var h = 0; h < arrJiShu2.length; h++) {
            jishu_child1.push(parseInt(arrJiShu2[h]) % 10);
            jishu_child2.push(parseInt(arrJiShu2[h]) / 10);

        }
        var sumJiShu = 0; //奇数位*2 < 9 的数组之和
        var sumOuShu = 0; //偶数位数组之和
        var sumJiShuChild1 = 0; //奇数位*2 >9 的分割之后的数组个位数之和
        var sumJiShuChild2 = 0; //奇数位*2 >9 的分割之后的数组十位数之和
        var sumTotal = 0;
        for (var m = 0; m < arrJiShu.length; m++) {

            sumJiShu = sumJiShu + parseInt(arrJiShu[m]);

        }
        for (var n = 0; n < arrOuShu.length; n++) {

            sumOuShu = sumOuShu + parseInt(arrOuShu[n]);
        }
        for (var p = 0; p < jishu_child1.length; p++) {
            sumJiShuChild1 = sumJiShuChild1 + parseInt(jishu_child1[p]);
            sumJiShuChild2 = sumJiShuChild2 + parseInt(jishu_child2[p]);
        }
        //计算总和
        sumTotal = parseInt(sumJiShu) + parseInt(sumOuShu) + parseInt(sumJiShuChild1) + parseInt(sumJiShuChild2);

        //计算Luhm值
        var k = parseInt(sumTotal) % 10 == 0 ? 10 : parseInt(sumTotal) % 10;
        var luhm = 10 - k;

        if (lastNum == luhm) {
            //$("#banknoInfo").html("Luhm验证通过");
            return true;
        } else {
            //$("#banknoInfo").html("银行卡号必须符合Luhm校验");
            return false;

        }

    }

    var cnbank = function(gets, obj, curform, regxp) {
        var msg = "";
        var strBin = "10,18,30,35,37,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,58,60,62,65,68,69,84,87,88,94,95,98,99";
        if (gets != "") {
            if (strBin.indexOf(gets.substring(0, 2)) == -1) {
                msg = "银行卡号开头6位不符合规范";
                $(obj).attr("errmsg", msg);
                return false;
            } else {
                if (gets.length < 16 || gets.length > 19) {
                    msg = "银行卡号长度必须在16到19之间";
                    $(obj).attr("errmsg", msg);
                    return false;
                } else {
                    if (luhmCheck($(obj).val())) {
                        return true;
                    } else {
                        msg = "请输入正确的银行卡号";
                        $(obj).attr("errmsg", msg);
                        return false
                    }

                }
                return true;
            }
        } else {
            return false;
        }
    }

    //单图上传
    var imgSingleFn = function(gets, obj, curform, regxp) {
        var src = $(obj).closest("div").siblings(".add-img-wrap").find("img").attr("src");
        if (src == "static/img/addimage.png") {
            return false;
        } else {
            return true;
        }

    }

    //多图上传
    var imgMoreFn = function(gets, obj, curform, regxp) {
        var len = $(obj).closest("div").siblings(".pro-wrap").find(".pre-img-wrap").length;
        if (len == 0) {
            return false;
        } else {
            return true;
        }
    }

    //多图上传
    var fileFn = function(gets, obj, curform, regxp) {
        var len = $(obj).parents(".form-group").find(".item").length;
        if (len == 0) {
            return false;
        } else {
            return true;
        }
    }




    window.valider = [];
    $("form.form-horizontal").each(function(idx, item) {
            $(this).data('validIndex', idx);
            var valid = $(this).Validform({
                tiptype: function(msg, o, cssctl) {
                    var parent = $(o.obj).closest('.validform-wrap');
                    parent.length == 0 && (parent = $(o.obj).closest('div'));
                    if (o.type == 3) {
                        parent.addClass("has-error");
                        var str = '<span class="Validform_checktip Validform_wrong"><i class="fa fa-times-circle"></i> ' + msg + '</span>';
                        parent.children(".Validform_checktip").remove();
                        parent.append(str);
                        $(o.obj).closest("div.form-group").find(".control-label").css("color", "#ed5565");
                    } else {
                        $(o.obj).closest("div.form-group").find(".control-label").css("color", "#1ab394");
                        parent.removeClass("has-error").addClass("has-success");
                        if ($(o.obj).attr("sucmsg")) {
                            parent.children(".Validform_checktip").addClass("Validform_right").html('<i class="fa fa-check-circle-o"></i> ' + $(o.obj).attr("sucmsg"));
                        } else {
                            parent.children(".Validform_checktip").remove();
                        }
                    }
                },
                showAllError: true,
                //ignoreHidden:true,
                datatype: { //自定义方法
                    "password": passwordFn,
                    "id": IdCard,
                    "pp": passCard,
                    "cnbank": cnbank,
                    "checkone": checkoneFn, //自定义调用
                    "imgSingle": imgSingleFn, //单图上传
                    "imgMore": imgMoreFn, //多图上传
                    "file": fileFn //文件上传
                },
                beforeSubmit: function(curform) {
                    $(curform).find('button[type=submit]').attr('disabled', 'disabled');
                    var submitCallback = $(curform).data("callback");
                    if (submitCallback != undefined) {
                        typeof window[submitCallback] == 'function' && window[submitCallback](curform);
                        return false;
                    }
                }
            });

            valider.push(valid);
        })
        //tipsmsg 属性
    $("form").find("[tipsmsg]").each(function() {
        var str = '<span class="Validform_checktip"><i class="fa fa-info-circle"></i> ' + $(this).attr('tipsmsg') + '</span>';
        $(this).closest("div").append(str);
    })

    //验证------------------------------------------end-------
})