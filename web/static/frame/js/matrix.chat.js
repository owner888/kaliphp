//输入框高度自适应
(function ($) {
    function FT(elem) {
        this.$textarea = $(elem);
        this._init();
    }
    FT.prototype = {
        _init: function () {
            var _this = this;
            this.$textarea.wrap('<div class="flex-text-wrap" />').before('<pre class="pre"><span /><br /></pre>');
            this.$span = this.$textarea.prev().find('span');
            this.$textarea.on('input propertychange keyup change', function () {
                _this._mirror();
            });
            $.valHooks.textarea = {
                get: function (elem) {
                    return elem.value.replace(/\r?\n/g, "\r\n");
                }
            };
            this._mirror();
        }
        ,_mirror: function () {
            this.$span.text(this.$textarea.val());
        }
    };
    $.fn.flexText = function () {
        return this.each(function () {
            // Check if already instantiated on this elem
            if (!$.data(this, 'flexText')) {
                // Instantiate & store elem + string
                $.data(this, 'flexText', new FT(this));
            }
        });
    };

})(jQuery);

//表情包
$(function() {
    $.fn.smohanfacebox = function(options) {
        var defaults = {
            Event : "click", //响应事件
            divid : "Smohan_FaceBox", //表单ID（textarea外层ID）
            textid : "TextArea" //文本框ID
        };
        var options = $.extend(defaults,options);
        var $btn = $(this);//取得触发事件的ID

        //创建表情框
        var faceimg1 = '',faceimg2 = '';
        for(i=0;i<all.length;i++){  //通过循环创建第一页表情，可扩展
           /* if(i<=112){
                faceimg1+='<li><a href="javascript:void(0)"><img src="static/img/face/'+(i+1)+'.png" data-i="['+i+']" face="['+all[i]+']" title="'+all[i]+'"/></a></li>';
            }else{
                faceimg2+='<li><a href="javascript:void(0)"><img src="static/img/face/'+(i+1)+'.png" data-i="['+i+']" face="['+all[i]+']" title="'+all[i]+'"/></a></li>';
            }*/
            if(i<=104){
                var cot = parseInt(i/15);
                var col = i;
                if(i>14){
                    col = i%15;
                }
                var x = -cot*29+'px';
                var y = -col*29+"px";
                faceimg1+='<li><a href="javascript:void(0)" face="['+all[i]+']" title="'+all[i]+'" style="background-position: '+y+' '+x+'"></a></li>';
            }else{
                cot = parseInt((i-105)/15);
                col = i;
                if(i<120){
                    x = 2+'px';
                }else{
                    x = (-cot+1)*2+-cot*30+'px';
                }
                col = -(col%15);
                y = col*30+(col+1)*2+"px";
                faceimg2+='<li><a href="javascript:void(0)" face="['+all[i]+']" title="'+all[i]+'" class="face2" style="background-position: '+y+' '+x+'"></a></li>';
            }

        };

        $("#"+options.divid).prepend("<div id='SmohanFaceBox'><div class='Content'><h3><span class='active'>常用表情</span><span>符号表情</span><a class='close' title='关闭'></a></h3><ul class='active'>"+faceimg1+"</ul><ul>"+faceimg2+"</ul></div></div>");
        $('#SmohanFaceBox').css("display",'none');//创建完成后先将其隐藏
        //创建表情框结束
        var $facepic = $("#SmohanFaceBox li a");
        //BTN触发事件，显示或隐藏表情层
        $btn.on(options.Event,function(e) {
            if($('#SmohanFaceBox').is(":hidden")){
                $('#SmohanFaceBox').show(360);
                $btn.addClass('in');
            }else{
                $('#SmohanFaceBox').hide(360);
                $btn.removeClass('in');
            }
        });
        //插入表情
        $facepic.off().click(function(){
            $('#SmohanFaceBox').hide(360);
            $("#"+options.textid).off().insertContent($(this).attr("face"));
            $btn.removeClass('in');

            $(".flex-text-wrap pre span").text($("#"+options.textid).val());
            $(".voice-wrap img.voice").hide();
            $(".voice-wrap button").show();
        });
        //关闭表情层
        $('#SmohanFaceBox h3 a.close').click(function() {
            $('#SmohanFaceBox').hide(360);
            $btn.removeClass('in');
        });
        //当鼠标移开时，隐藏表情层，如果不需要，可注释掉
        $('#SmohanFaceBox').mouseleave(function(){
            $('#SmohanFaceBox').hide(560);
            $btn.removeClass('in');
        });

        $("body").find(".Content h3 span").each(function(i){
            $(this).click(function(){
                var i = $(this).index();
                $(this).addClass("active").siblings("span").removeClass("active");
                $(this).parents("h3").siblings("ul").eq(i).show().siblings("ul").hide();
            })

        })

    };

    // 【漫画】 光标定位插件
    $.fn.extend({
        insertContent : function(myValue, t) {
            var $t = $(this)[0];
            if (document.selection) {
                this.focus();
                var sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
                sel.moveStart('character', -l);
                var wee = sel.text.length;
                if (arguments.length == 2) {
                    var l = $t.value.length;
                    sel.moveEnd("character", wee + t);
                    t <= 0 ? sel.moveStart("character", wee - 2 * t	- myValue.length) : sel.moveStart("character", wee - t - myValue.length);
                    sel.select();
                }
            } else if ($t.selectionStart || $t.selectionStart == '0') {
                var startPos = $t.selectionStart;
                var endPos = $t.selectionEnd;
                var scrollTop = $t.scrollTop;
                $t.value = $t.value.substring(0, startPos) + myValue + $t.value.substring(endPos,$t.value.length);
                this.focus();
                $t.selectionStart = startPos + myValue.length;
                $t.selectionEnd = startPos + myValue.length;
                $t.scrollTop = scrollTop;
                if (arguments.length == 2) {
                    $t.setSelectionRange(startPos - t,$t.selectionEnd + t);
                    this.focus();
                }
            } else {
                this.value += myValue;
                this.focus();

            }
            $($t).on("input propertychange keyup change",function(e){
                var val = $($t).val();
                $(".flex-text-wrap pre span").text(val);
                if(val.length>0){
                    $(".voice-wrap img.voice").hide();
                    $(".voice-wrap button").show();
				}else{
                    $(".voice-wrap img.voice").show();
                    $(".voice-wrap button").hide();
				}
                if(e.which == 13) {
                    if($(this).val() != ''){
                        add_message('You','static/img/demo/av1.jpg',$(this).val(),true);
                        $(this).val("");
                        $(".flex-text-wrap pre span").text("");
                        $(".voice-wrap img.voice").show();
                        $(".voice-wrap button").hide();
                        $("#chat-messages-inner ").replaceface($("#chat-messages-inner").html());
                        setTimeout(function(){
                            $('.chat-message textarea').focus();
                        },100);
                    }
                }
            })
        }
    });

    //表情解析
    var all = [
        "微笑", "撇嘴", "色", "发呆", "得意", "流泪", "害羞", "闭嘴", "睡", "大哭", "尴尬", "发怒",
        "调皮", "呲牙", "惊讶", "难过", "酷", "冷汗", "抓狂", "吐", "偷笑", "愉快", "白眼", "傲慢", "饥饿",
        "困", "惊恐", "流汗", "憨笑", "悠闲", "奋斗", "咒骂", "疑问", "嘘", "晕", "疯了", "衰", "骷髅",
        "敲打", "再见", "擦汗", "抠鼻", "鼓掌", "糗大了", "坏笑", "左哼哼", "右哼哼", "哈欠", "鄙视", "委屈",
        "快哭了", "阴险", "亲亲", "吓", "可怜", "菜刀", "西瓜", "啤酒", "篮球", "乒乓", "咖啡", "饭", "猪头", "玫瑰",
        "凋谢", "嘴唇", "爱心", "心碎", "蛋糕", "闪电", "炸弹", "刀", "足球", "瓢虫", "便便", "月亮", "太阳", "礼物",
        "拥抱", "强", "弱", "握手", "胜利", "抱拳", "勾引", "拳头", "差劲", "爱你", "NO", "OK", "爱情", "飞吻", "跳跳",
        "发抖", "怄火", "转圈", "磕头", "回头", "跳绳", "投降", "激动","乱舞","献吻","左太极","右太极",
        "笑脸", "生病", "破涕为笑", "吐舌", "脸红", "恐惧", "失望", "无语", "嘿哈", "捂脸", "奸笑", "机智",
        "皱眉", "耶", "鬼魂", "合十", "强壮", "庆祝", "礼物", "红包", "鸡", "开心", "大笑", "热情",
        "眨眼", "色", "接吻", "亲吻", "露齿笑", "满意", "戏弄", "得意", "汗", "低落", "呸", "焦虑",
        "担心", "震惊", "悔恨", "眼泪", "哭", "晕", "心烦", "生气", "睡觉", "恶魔", "外星人", "心",
        "心碎", "丘比特", "闪烁", "星星", "叹号", "问号", "睡着", "水滴", "音乐", "火", "便便", "强",
        "弱", "拳头", "胜利", "上", "下", "右", "左", "第一", "吻", "热恋", "男孩", "女孩", "女士",
        "男士", "天使", "骷髅", "红唇", "太阳", "下雨", "多云", "雪人", "月亮", "闪电", "海浪", "猫",
        "小狗", "老鼠", "仓鼠", "兔子", "狗", "青蛙", "老虎", "考拉", "熊", "猪", "牛", "野猪", "猴子", "马", "蛇",
        "鸽子", "鸡", "企鹅", "毛虫", "章鱼", "鱼", "鲸鱼", "海豚", "玫瑰", "花", "棕榈树", "仙人掌", "礼盒", "南瓜灯",
        "圣诞老人", "圣诞树", "铃", "气球", "CD", "相机", "录像机", "电脑", "电视", "电话", "解锁", "锁", "钥匙", "成交",
        "灯泡", "邮箱", "浴缸", "钱", "炸弹", "手枪", "药丸", "橄榄球", "篮球", "足球", "棒球", "高尔夫", "奖杯", "入侵者",
        "唱歌", "吉他", "比基尼", "皇冠", "雨伞", "手提包", "口红", "戒指", "钻石", "咖啡", "啤酒", "干杯", "鸡尾酒", "汉堡",
        "薯条", "意面", "寿司", "面条", "煎蛋", "冰激凌", "蛋糕", "苹果", "飞机", "火箭", "自行车", "高铁", "警告", "旗", "男人",
        "女人", "O", "X", "版权", "注册商标", "商标"
    ]

    //表情解析
    $.fn.extend({
        replaceface : function(faces){
            var len = all.length;
            console.log(len)
            for(i=0;i<len;i++){
                if(i<=104){
                    var cot = parseInt(i/15);
                    var col = i;
                    if(i>14){
                        col = i%15;
                    }
                    var x = -cot*29+'px';
                    var y = -col*29+"px";
                    faces=faces.replace('['+ all[i] +']','<i style="width:30px;height:30px;display: inline-block;vertical-align: middle;background-position: '+y+' '+x+'"></i>');
                }else{
                    cot = parseInt((i-105)/15);
                    col = i;
                    if(i<120){
                        x = 2+'px';
                    }else{
                        x = (-cot+1)*2+-cot*30+'px';
                    }
                    col = -(col%15);
                    y = col*30+(col+1)*2+"px";
                    faces=faces.replace('['+ all[i] +']','<i style="width:30px;height:30px;display: inline-block;vertical-align: middle;background-position: '+y+' '+x+'" class="face2"></i>');
                }
            }
            $(this).html(faces);
        }
    });
});

//文件上传
$(function(){

    var $btn = $('.uploader-btn');

    uploader = new Array();
    $('.uploader-group').each(function(index){

        var tmp_path   = $(this).data("dir");
        var randname   = $(this).data("randname");
        var multiple   = $(this).data("multiple");
        var extensions = $(this).data("extensions");
        var mimeTypes  = $(this).data("mimeTypes");
        var auto = $(this).data("auto");  //是否自动上传
        var len = $(this).data("len");    //是否单独上传
        var size = $(this).data("size")*1024*1024;//文件大小限制 ,配置单位M;
        if( $(this).data("size")==undefined){
            size=1000000000000000;
        }
        if (tmp_path == undefined) {
            tmp_path = 'tmp';
        };
        if (randname == undefined) {
            randname = '1';
        };
        if (multiple == undefined) {
            multiple = true;
        };
        if (extensions == undefined) {
            extensions = 'gif,jpg,jpeg,bmp,png,zip,pdf,doc,xls';
        };
        if (mimeTypes == undefined) {
            mimeTypes = 'image/*,application/zip,application/pdf,application/msword,application/vnd.ms-excel';
        };

        var filePicker=$(this).find('.uploader-picker');//上传按钮实例
        uploader[index] = WebUploader.create({
            resize: false,                          // 不压缩image
            swf: 'static/webuploader/uploader.swf', // swf文件路径
            server: '?ct=upload&ac=webuploader',    // 文件接收服务端。
            //pick: '.btn-dark',
            // 选择文件的按钮。可选
            pick:{
                id:filePicker,                      // 选择文件的按钮。可选
                multiple: multiple,                 // 默认为true，就是可以多选
            },
            duplicate: true,
            chunked: true,                          // 是否要分片处理大文件上传
            chunkSize: 1*1024*1024,                 // 分片上传，每片2M，默认是5M
            auto: auto,                            // 选择文件后是否自动上传
            chunkRetry : 2,                         // 如果某个分片由于网络问题出错，允许自动重传次数
            runtimeOrder: 'html5,flash',
            accept: {
                title: 'Images',
                extensions: extensions,
                mimeTypes: mimeTypes
            },
            formData: {
                //'token'     :index ,
                'randname'  : randname,             // 是否随机生成文件名
                'tmp_path'  : tmp_path,             // 上传文件目录
            }
        });
        uploader[index].on( 'fileQueued', function( file ) {
            var m = size/1024/1024;
            if(file.size<size){
                if(file.type=="image/png"){ //判断是否是图片
                    addFile( file,uploader[index]);
                }else{
                    i = i + 1;
                    var  inner = $('#chat-messages-inner');
                    var time = new Date();
                    var hours = time.getHours();
                    var minutes = time.getMinutes();
                    if(hours < 10) hours = '0' + hours;
                    if(minutes < 10) minutes = '0' + minutes;
                    var id = 'msg-'+i;
                    var idname = name.replace(' ','-').toLowerCase();
                    inner.append('<p id="'+id+'" class="user-'+idname+' item-wrap">'
                        +'<span class="msg-block">' +
                        '<img src="static/img/demo/av3.jpg" alt="">' +
                        '<strong>'+name+'</strong> ' +
                        '<span class="time">- '+hours+':'+minutes+'</span>'
                        +'<span class="msg item file-item" id="'+ file.id+'">' +
                        '<span class="info">' + file.name + '<i class="fa fa-close close-btn" style="color:red"></i></span>' +
                        '<span class="state">等待上传...</span>' +
                        '</span></span></p>');
                    $('#'+id).hide().fadeIn(800);

                    $('#chat-messages').animate({ scrollTop: inner.height() },1000);

                }
            }else{
                parent.layer.msg("文件大小不能超过"+m+"M");
                return false;
            }
        });

        // 当有img添加进来时执行，负责view的创建
        function addFile( file,now_uploader) {
            now_uploader.makeThumb(file, function (error, src) {
                i = i + 1;
                var  inner = $('#chat-messages-inner');
                var time = new Date();
                var hours = time.getHours();
                var minutes = time.getMinutes();
                if(hours < 10) hours = '0' + hours;
                if(minutes < 10) minutes = '0' + minutes;
                var id = 'msg-'+i;
                var idname = "You";
                inner.append('<p id="'+id+'" class="user-'+idname+' item-wrap">'
                    +'<span class="msg-block">' +
                    '<img src="static/img/demo/av3.jpg" alt="">' +
                    '<strong>'+idname+'</strong> ' +
                    '<span class="time">- '+hours+':'+minutes+'</span>'
                    +'<span class="msg item img-item" id="'+ file.id+'">' +
                    '<img style="width:100px;height:100px;" src="'+src+'">' +
                    '<i class="fa fa-close close-btn"></i>' +
                    '<span class="state">等待上传...</span>' +
                    '</span></span>' +
                    '</p>');
                $('#'+id).hide().fadeIn(800);

                $('#chat-messages').animate({ scrollTop: inner.height() },1000);

            });
        }


        //加入队列前，判断文件格式，不合适的排除
        uploader[index].on('beforeFileQueued', function (file) {
            file.guid = WebUploader.Base.guid();
        });
        //文件分块上传前触发，加参数，文件的订单编号加在这儿
        uploader[index].on('uploadBeforeSend', function (object, data, headers) {
            //console.log(object);
            data.guid = object.file.guid;
        });
        // 文件上传过程中创建进度条实时显示。
        uploader[index].on( 'uploadProgress', function( file, percentage ) {
            var $li = $( '#'+file.id),
                $percent = $li.find('.progress .progress-bar');
            // 避免重复创建
            if ( !$percent.length ) {
                $percent = $('<span class="progress progress-striped active">' +
                    '<span class="progress-bar" role="progressbar" style="width: 0%">' +
                    '</span>' +
                    '</span>').appendTo( $li ).find('.progress-bar');
            }

            $li.find('p.state').text('上传中');
            $percent.css( 'width', percentage * 100 + '%' );
        });

        // 文件上传成功
        uploader[index].on( 'uploadSuccess', function( file,response ) {
            var obj = JSON.parse(response._raw);
            //console.log(obj.result.filename);
            //console.log(obj.result.filelink);
            var name =  $( '#'+file.id ).parents(".uploader-list").siblings("a").data("file"),
                val = obj.result.filename,
                src = obj.result.filelink;
            var str = '<input type="hidden" name="'+name+'[]" value="'+val+'" class="hid-filename">'
            $( '#'+file.id ).append(str);
            $( '#'+file.id).find("img").attr("src",src);
            $( '#'+file.id ).find('p.state').text('上传完成');
            if(len==1){
                $( '#'+file.id ).parents(".uploader-list").siblings("a").addClass("hide");
                hideBtnFn();
            }
            return false
        });
        // 文件上传失败，显示上传出错
        uploader[index].on( 'uploadError', function( file ) {
            $( '#'+file.id ).find('p.state').text('上传出错');
        });

        // 完全上传完
        uploader[index].on( 'uploadComplete', function( file ) {
            $( '#'+file.id ).find('.progress').fadeOut();
        })

    })

    //每个上传button加data-id
    $(".uploader-btn").each(function(index){
        $(this).attr("data-id",index);
    })


    $btn.on('click', function () {
        if ($(this).hasClass('disabled')) {
            return false;
        }
        $(this).siblings(".uploader-list").addClass("AF");
        var i = $(this).data("id");
        uploader[i].upload();

    });

    $("body").on("click",".close-btn",function(){  //点击删除按钮
        var val = $(this).parents(".item").find("input[type=hidden]").val();
        var len = $(this).parents(".uploader-group").data("len");
        var item = $(this).parents(".item");
        var str = $(this).parents(".item").find(".state").html();
        if(str == "上传完成"){ //判断删除时是否为已上传
            if (val == undefined || val == "" || val == null) {
                return false;
            };
            var tmp_path = $(this).parents(".uploader-group").data("dir");
            if (tmp_path == undefined) {
                tmp_path = 'tmp';
            };
            //console.log(tmp_path);

            $.getJSON("?ct=upload&ac=file_delete", {"filename":val, "tmp_path":tmp_path},function(result){
                if (result.code == 0) {
                    item.remove();
                } else {
                    alert(result.msg);
                }
            });
        }else{
            item.remove();
        }
    })

});


$(document).ready(function(){
	var msg_template = '<p><span class="msg-block"><strong></strong><span class="time"></span><span class="msg"></span></span></p>';
	$('.chat-message button').click(function(){
		var input = $(this).parents(".chat-footer").find('textarea');
		if(input.val() != ''){
			add_message('You','static/img/demo/av1.jpg',input.val(),true);
		}
        input.val("");
		$(".flex-text-wrap span").html("");
        $(".voice-wrap img.voice").show();
        $(".voice-wrap button").hide();
        setTimeout(function(){
            $('.chat-message textarea').focus();
        },100)
        $("#chat-messages-inner ").replaceface($("#chat-messages-inner").html());
	});

	$("#msg-box").on("input",function(){
		var len = $(this).val().length;
		if(len>=1){
			$(".voice-wrap img.voice").hide();
			$(".voice-wrap button").show();
		}else{
            $(".voice-wrap img.voice").show();
            $(".voice-wrap button").hide();
		}
	})
	
	$('.chat-message textarea').keypress(function(e){
		if(e.which == 13) {
			if($(this).val() != ''){
				add_message('You','static/img/demo/av1.jpg',$(this).val(),true);
                $(this).val("");

                setTimeout(function(){
                    $('.chat-message textarea').focus();
                },100)
                $(".voice-wrap img.voice").show();
                $(".voice-wrap button").hide();
                $("#chat-messages-inner ").replaceface($("#chat-messages-inner").html());
                //return;

			}
		}
	});
	
	setTimeout(function(){
			add_message('Linda','static/img/demo/av2.jpg','Hello Every one do u want to freindship with me?')
		},'1000');
	setTimeout(function(){
			add_message('Mark','static/img/demo/av3.jpg','Yuppi! why not sirji!!.')
		},'4000');
	setTimeout(function(){
			add_message('Linda','static/img/demo/av2.jpg','Thanks!!! See you soon than')
		},'8000');
	setTimeout(function(){
			add_message('Mark','static/img/demo/av3.jpg','ok Bye than!!!.')
		},'12000');	
	setTimeout(function(){
            remove_user('Linda','Linda')
        },'16000');
   	var i = 0;



    function remove_user(userid,name) {
        i = i + 1;
        $('.contact-list li#user-'+userid).addClass('offline').delay(1000).slideUp(800,function(){
            $(this).remove();
        });
        var  inner = $('#chat-messages-inner');
        var id = 'msg-'+i;
        inner.append('<p class="offline" id="'+id+'"><span>User '+name+' left the chat</span></p>');
        $('#'+id).hide().fadeIn(800);
    }
});
function add_message(name,img,msg,clear) {
    //msg = msg.replace(/[\n\r]/g, '<br>')
    i = i + 1;
    var  inner = $('#chat-messages-inner');
    var time = new Date();
    var hours = time.getHours();
    var minutes = time.getMinutes();
    if(hours < 10) hours = '0' + hours;
    if(minutes < 10) minutes = '0' + minutes;
    var id = 'msg-'+i;
    var idname = name.replace(' ','-').toLowerCase();
    inner.append('<p id="'+id+'" class="user-'+idname+'">'
        +'<span class="msg-block"><img src="'+img+'" alt="" /><strong>'+name+'</strong> <span class="time">- '+hours+':'+minutes+'</span>'
        +'<span class="msg">'+msg+'</span></span></p>');
    //$('#'+id).hide().fadeIn(800);
    if(clear) {
        $('.chat-message input').val('').focus();
    }
    $('#chat-messages').animate({ scrollTop: inner.height() },1000);
}