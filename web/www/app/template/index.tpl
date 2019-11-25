<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title><{$title}></title>
    <link href="static/img/favicon.ico" rel="shortcut icon">
    <link href="static/frame/css/bootstrap.min14ed.css" rel="stylesheet">
    <link href="static/frame/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="static/frame/css/animate.min.css" rel="stylesheet">
    <link href="static/frame/ui2/css/style.css" rel="stylesheet">
    <link href="static/frame/css/jquery.gritter.css" rel="stylesheet">
    <link href="static/frame/css/plugins/toastr/toastr.min.css" rel="stylesheet">
    <script src="static/frame/js/jquery.min.js?v=2.1.4"></script>
    <script src="static/frame/js/common.js"></script>
</head>

<body class="fixed-sidebar full-height-layout gray-bg" >
    <!--顶部导航-->
    <div id="wrapper">
        <nav class="navbar navbar-static-top" role="navigation">
            <div class="nav-header">
                <div class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                        <span><{$title}></span>
                        <img src="static/frame/img/logo-white-min.svg" alt="">
                        <!--<i class="fa fa-caret-down"></i>-->
                    </a>
                    <!--<ul class="dropdown-menu">-->
                        <!--<li><a href="#">公司1</a></li>-->
                        <!--<li><a href="#">公司2</a></li>-->
                        <!--<li><a href="#">公司3</a></li>-->
                        <!--</ul>-->
                </div>
                <!--img src="static/frame/img/logo-min.png" alt="" class="logo-img" / -->
            </div>
            <div class="navbar-container container-fluid">
                <a class="navbar-minimalize minimalize-styl-2 btn  pull-left" href="#">
                    <i class="fa  fa-bars" style="display: none;"></i>
                    <img src="static/frame/img/left-arrow.svg" alt=""   class="img-arrow" style="width:20px">
                </a>
                <a class="navbar-minimalize minimalize-styl-2 minimalize-styl-3 btn  pull-left" href="#">
                    <i class="fa fa-bars"></i>
                    <img src="static/frame/img/left-arrow.svg" alt=""   class="img-arrow" style="width:20px;display: none;">
                </a>
                <ul class="pull-left nav-left">
                    <div class="dropdown pull-left" id="navbarSubMenu">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" data-animation="slide-bottom" aria-expanded="true" role="button">
                            <i class="fa fa fa-ellipsis-v"></i>
                        </a>
                        <ul class="dropdown-menu" role="menu">

                        </ul>
                    </div>
                </ul>

                <div class="pull-right nav-right">
                    <div class="dropdown notify-wrap pull-left" data-toggle="tooltip" data-placement="bottom" title="" >
                        <a data-toggle="" href="javascript:;" class="msg-btn" aria-expanded="false" data-animation="scale-up" role="button">
                            <i class="fa fa fa-bell-o" aria-hidden="true"></i>
                            <span class="badge badge-danger up msg-num" style="display:none">0</span>
                        </a>
                    </div>
                    <a href="javascript:;" class="screen pull-left" id="screen-btn" data-toggle="tooltip"
                    data-placement="bottom" title="全屏" data-trigger="hover"><i class="fa  fa-arrows"></i></a>
                    <a href="?ac=logout" class="pull-left  J_tabExit" data-toggle="tooltip" data-placement="bottom" title="退出"><i class="fa fa fa-sign-out"></i></a>
                    <div class="user-info-wrap pull-left dropdown ">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="true">
                            <img src="static/frame/img/user-info.png" alt="" class="pull-left">
                            <div class="pull-left user-info-r">
                                <div class="user-info-r-inner">
                                    <h4><{$user.username}></h4>
                                    <p><{$user.realname}></p>
                                </div>

                            </div>
                        </a>
                        <!--ul class="dropdown-menu " id="user-type-wrap">
                            <li class="m-t-xs">
                                <a href="">身份1</a>
                            </li>

                            <li>
                                <a href="">身份2</a>
                            </li -->
                        </ul>
                    </div>

                </div>
            </div>
            <div class="muen-icon-wrap open">
                <i class="fa fa-ellipsis-h"></i>
            </div>
        </nav>
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">

                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg ">
            <div class=" content-tabs">
                <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
                </button>
                <nav class="page-tabs J_menuTabs">
                    <div class="page-tabs-content">
                        <!--<a href="javascript:;" class="active J_menuTab" data-id="approve.html" data-index="9">审批申请</a>-->
                    </div>
                </nav>
                <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
                </button>
                <button class="roll-nav refresh-btn" onclick="fnIfram()"><i class="fa fa-refresh"></i>
                </button>
                <div class="btn-group roll-nav roll-right">
                    <button class="dropdown J_tabClose" data-toggle="dropdown">关闭操作<span class="caret"></span> </button>
                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                        <li class="J_tabShowActive"><a>定位当前选项卡</a> </li>
                        <li class="divider"></li>
                        <li class="J_tabCloseAll"><a>关闭全部选项卡</a> </li>
                        <li class="J_tabCloseOther"><a>关闭其他选项卡</a> </li>
                    </ul>
                </div>
            </div>
            <div class="J_mainContent" id="content-main" >
                <!--<iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="approve.html" frameborder="0" data-id="approve.html" seamless></iframe>-->
            </div>

        </div>
        <!--右侧部分结束-->
    </div>

    <!--消息中心-->
    <ul class="msg-center" role="menu">
        <li class="msg-header" >
            <span class="msg-header-title">消息中心</span>
            <a class="msg-header-more list-group-item" data-href="?ct=message" href="javascript:;" data-item="other" data-reload="false" data-message="消息中心">查看更多</a>
            <a class="msg-header-close"><i></i></a>
        </li>
        <li class="list-group" >
            <div class="slimScrollDiv" id="notifyWrap">
                <!-- <div class="msg-item" >
                    <div class="media-top">
                        <h4>系统消息</h4>
                        <span>2017-11-08 16:33:59</span>
                    </div>
                    <a class="media-con list-group-item" >
                        欢迎您访问Admui演示系统
                    </a>
                    <p class="media-tip">
                        标记已读
                    </p>
                </div>
                <div class="msg-item" >
                    <div class="media-top">
                        <h4>系统消息</h4>
                        <span>2017-11-08 16:33:59</span>
                    </div>
                    <a class="media-con list-group-item">
                        欢迎您访问Admui演示系统
                    </a>
                     <p class="media-tip">
                        标记已读
                    </p>
                </div> -->
                <p class="nomsg">暂无未读消息</p>
            </div>
        </li>
    </ul>
    <script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="static/frame/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="static/frame/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="static/frame/js/plugins/layer/layer.min.js"></script>
    <script src="static/frame/js/plugins/pace/pace.min.js"></script>
    <script src="static/frame/js/plugins/toastr/toastr.min.js"></script>
    <script src="static/frame/js/plugins/switch/switch.js"></script>
    <script src="static/frame/ui2/js/hplus.min.js?v=4.1.0"></script>
    <script src="static/frame/ui2/js/contabs.min.js"></script>
    <script src="static/frame/js/jquery.gritter.min.js"></script>
    <script src="static/frame/js/jquery.peity.min.js"></script>
    <script src="static/frame/js/interface.js"></script>
    <script src="static/frame/js/crypto-js.js<{$clear_cache}>"></script>
    <script src="static/frame/js/main.js<{$clear_cache}>"></script>
    <{if !empty($websocket_url) && !empty($websocket_key)}>
    <script>
    var WEBSOCKET_URL = '<{$websocket_url}>';
    var WEBSOCKET_KEY = '<{$websocket_key}>';
    </script>
    <script src="static/frame/js/websocket.js<{$clear_cache}>"></script>
    <{/if}>
    <script>
        var MenuData = <{$menus}>;

        //子iframe调用管理关闭dropdown
        function closeDrop(e,type){
            if($(e.target).parents(".dropdown").length == 0){
                if($(".notify-wrap").hasClass("open")){  //判断是否为消息
                    $("#notifyWrap .msg-item").addClass("invalid-item");
                    $(".msg-num").html("0").hide();
                }
            }
            if(type=="child" && parent.$(".notify-wrap").hasClass("open")){
                parent.$(".notify-wrap").removeClass("open");
            }
            
        }

        //顶部icon提示
        $(function () { $("[data-toggle='tooltip']").tooltip(); });
       
        //消息弹出
        /*$("body").messageFn({
            title:"最新消息",            //tab标题
            text:"开业啦阿发舒服",        //内容
            url:"?ct=content&ac=index", //链接
            time:"2017-09-08 09：00",   //时间
            reload:true                 //是否要刷新页面
        }) $("body").notifyFn({
            title:"",            //tab标题
            text:"最新消息",              //时间
            url:"home.html",             //链接
            time:"2017-09-08 09：00",    //内容
            reload:true                  //是否要刷新页面
        })
        */ 
  
        function fnIfram() {
            $("body").on("click",".refresh-btn",function(){
                var ifr = $("iframe");
                for(var i = 0;i<ifr.length;i++){
                    if(ifr.eq(i).css("display")=="inline") {
                        ifr[i].contentWindow.location.reload(true);
                    }
                }
            })
        }

        /*
         * 内页跳转调用
         * url当前跳转的url
         * oldurl：url截取匹对的url
         * */
        function pageJumps(url, oldurl) {
            $('#side-menu a[href="' + oldurl + '"]').attr('href', url);
            $(".page-tabs-content").find(".J_menuTab").each(function() {
                if ($(this).data("id").indexOf(oldurl) != -1) {
                    $(this).attr("data-id", url)
                }
            });
            $('#side-menu a[href="' + url + '"]').trigger('click');
            $('#side-menu a[href="' + url + '"]').attr('href', oldurl);
            $('#side-menu a[href="' + url + '"]').parents('li').addClass('active');
            $('#side-menu a[href="' + url + '"]').parents('ul').addClass('in');
        }
        /*
         * 表单提交跳转调用
         * url当前跳转的url
         * nowurl：当前所在url 如果要关闭当前页就传
         * */
        function formJumps(url, nowurl) {
            $('#side-menu a[data-page="' + url + '"]').trigger('click');
            if (nowurl != undefined) {
                $(".page-tabs-content").find(".J_menuTab").each(function() {
                    if ($(this).data("id").indexOf(nowurl) != -1) {
                        $(this).remove();
                    }
                })
            }
            $('#side-menu a[data-page="' + url + '"]').parents('li').addClass('active');
            $('#side-menu a[data-page="' + url + '"]').parents('ul').addClass('in');
        }

        function is_switch(){
            $('.lcs_check').lc_switch();
            $('body').delegate('.lcs_check', 'lcs-statuschange', function() {
                var status = ($(this).is(':checked')) ? 'checked' : 'unchecked';
                if(status=="checked"){
                    $(this).attr("checked","checked")
                }else{
                    $(this).removeAttr("checked")
                }
                var height = 130;
                if($(this).attr("name")=="check1"){
                    if($(this).parents(".form-group").siblings(".one-item").css("display")=="none"){
                        height = 80
                    }else{
                        height=130;
                    }
                    if($(this).attr("checked")!=undefined){
                        $(this).parents(".form-group").siblings(".enter-form").hide();
                        $(this).closest(".layui-layer-content").css("height",height);
                    }else{
                        $(this).parents(".form-group").siblings(".enter-form").show();
                        $(this).closest(".layui-layer-content").css("height",height+50);  
                    }     
                }
            });
        }

        function showPassword(){
            $("body").on("click",".eye-btn",function(){
                var _this = $(this),
                    _input =$(".eye-btn").siblings("input");
                if(_input.attr("type")=="password"){
                    _input.attr("type","text")
                }else{
                    _input.attr("type","password")
                }
            })
            $("body").on("click",".copy-btn",function(){
                var Url2= $(".layui-layer-content").find("#password").val();
                var oInput = document.createElement('input');
                oInput.value = Url2;
                document.body.appendChild(oInput);
                oInput.select();              // 选择对象
                document.execCommand("Copy"); // 执行浏览器复制命令
                oInput.className = 'oInput';
                oInput.style.display='none';
                $(".oInput").remove();
                layer.msg('复制成功');
                return false
            })

            $("body").on("input","#edit-password",function(){
                var pwd_val = $(this).val();
                var lv = 0;
                if (pwd_val.match(/[A-Z]/g)) {
                    lv++;
                }
                if (pwd_val.match(/[a-z]/g)) {
                    lv++;
                }
                if (pwd_val.match(/[0-9]/g)) {
                    lv++;
                }
                if (pwd_val.length >= 6 && pwd_val.length <= 18) {
                    lv++
                }
                if (lv < 4) {
                    $(this).siblings("span").show();
                    return false;
                } else {
                        $(this).siblings("span").hide();
                }
            })
        }
        showPassword();

        
        /*$("body").toastr({
            type:'success',                   //提示类型 “success-成功” “info-提示” “warning-警告” error-错误
            text:"我是内容",                   //内容信息
            time:"2017-09-08 09：00",         //时间
            title:"",                         //标题信息 可不填
            url:"?ct=session&ac=online",      //点击跳转连接，为空时不跳转
            reload:true,                      //是否要刷新页面    
            closeButton:true,                 //关闭按钮 默认为true 可不填
            debug: true,                      //debug 默认为true 可不填
            progressBar: true,/               /进度条 默认为true 可不填
            positionClass: "toast-top-right", //位置 默认为右上角 toast-top-right toast-top-right toast-bottom-left toast-bottom-right
            showDuration: "400",              //显示持续时间 默认400 可不填
            hideDuration: "1000",             //隐藏持续时间 默认1000 可不填
            timeOut: "7000",                  //超时 默认7000 可不填
            extendedTimeOut: "1000",          //延长 默认7000 可不填
            showEasing: "swing",              //显示动画 默认swing 可不填
            hideEasing: "linear",             //隐藏动画 默认linear 可不填
            showMethod: "fadeIn",             //显示方法 默认fadeIn 可不填
            hideMethod: "fadeOut"             //隐藏方法 默认fadeOut 可不填
        })
        $("body").toastr({
            type:'info',
            text:"我是内容",
            time:"2017-09-08 09：00",  
            title:"我是标题info",
            url:"http://www.baidu.com",
            reload:true,               
            closeButton:true,
            debug: true,
            progressBar: true,
            positionClass: "toast-top-right"
        })
        $("body").toastr({
            type:'warning',
            text:"我是内容info",
            title:"我是标题iwarning",
            time:"2017-09-08 09：00",
            url:"",
            reload:true,
            closeButton:true,
            debug: true,
            progressBar: true,
            positionClass: "toast-top-right"       
        })
        $("body").toastr({
            type:'error',
            text:"我是内容info",
            time:"2017-09-08 09：00",
            title:"我是标题error",
            url:"?ct=content&ac=index",
            reload:true,  
            closeButton:true,
            debug: true,
            progressBar: true,
            positionClass: "toast-top-right"   
        })*/
    </script>
</body>

</html>
