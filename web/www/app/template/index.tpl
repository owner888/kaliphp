<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>

    <link href="static/ui/css/style.css?d=20241211" rel="stylesheet">
    <link href="static/css/jquery.gritter.css" rel="stylesheet">
</head>

<body class="fixed-sidebar full-height-layout gray-bg layer-frame">
    <!--顶部导航-->
    <div id="wrapper">
        <nav class="navbar navbar-static-top" role="navigation">
            <div class="nav-header">
                <div class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                        <span><{$app_name}></span>
                        <img src="static/img/logo-white-min.svg" alt="">
                        <!--<i class="fa fa-caret-down"></i>-->
                    </a>
                    <!--<ul class="dropdown-menu">-->
                        <!--<li><a href="#">公司1</a></li>-->
                        <!--<li><a href="#">公司2</a></li>-->
                        <!--<li><a href="#">公司3</a></li>-->
                        <!--</ul>-->
                </div>
                <!--img src="static/img/logo-min.png" alt="" class="logo-img" / -->
            </div>
            <div class="navbar-container container-fluid">
                <a class="navbar-minimalize minimalize-styl-2 btn  pull-left" href="#">
                    <i class="fa  fa-bars" style="display: none;"></i>
                    <img src="static/img/left-arrow.svg" alt=""   class="img-arrow" style="width:20px">
                </a>
                <a class="navbar-minimalize minimalize-styl-2 minimalize-styl-3 btn  pull-left" href="#">
                    <i class="fa fa-bars"></i>
                    <img src="static/img/left-arrow.svg" alt=""   class="img-arrow" style="width:20px;display: none;">
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
                    <a href="javascript:;" class="pull-left items" data-toggle="dropdown" title="切换多语言" data-trigger="hover"><i class="fa fa-language"></i></a>
                    <ul role="menu" class="dropdown-menu dropdown-menu-right list-menu-navigation" id="list-locals">
                        <li class="item" onclick="setLocal('en')">English</li>
                        <li class="item" onclick="setLocal('zh-cn')">简体中文</li>
                        <li class="item" onclick="setLocal('zh-tw')">繁体中文</li>
                    </ul>
                    <a href="javascript:;" class="pull-left items" id="tools-btn" data-toggle="tooltip" data-placement="bottom" title="n进制工具" data-trigger="hover"><i class="fa fa-wrench"></i></a>
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
                            <img src="static/img/user-info.png" alt="" class="pull-left">
                            <div class="pull-left user-info-r">
                                <div class="user-info-r-inner">
                                    <h4><{$user.username}></h4>
                                    <p><{$user.realname}></p>
                                </div>

                            </div>
                        </a>
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
    <{include file='common/footer.tpl'}>
    <script src="static/js/common.js"></script>
    <script src="static/js/plugins/metisMenu/jquery.metisMenu.js"></script>
    <script src="static/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
    <script src="static/js/plugins/pace/pace.min.js"></script>
    <script src="static/js/plugins/toastr/toastr.min.js"></script>
    <script src="static/js/plugins/switch/switch.js"></script>
    <script src="static/ui/js/hplus.min.js?v=4.1.0"></script>
    <script src="static/ui/js/contabs.min.js"></script>
    <script src="static/js/jquery.gritter.min.js"></script>
    <script src="static/js/jquery.peity.min.js"></script>
    <script src="static/js/plugins/suggest/bootstrap-suggest.min.js"></script>
    <script src="static/js/interface.js"></script>
    <script src="static/js/crypto-js.js<{$clear_cache}>"></script>
    <{if !empty($websocket_url) && !empty($websocket_key)}>
    <script>
    var WEBSOCKET_URL = '<{$websocket_url}>';
    var WEBSOCKET_KEY = '<{$websocket_key}>';
    </script>
    <script src="static/js/websocket.js<{$clear_cache}>"></script>
    <{/if}>
    <script>
        // 树状菜单
        const MenuData = <{$menus}>;

        // 二级菜单
        const MenuSubList = (function(MenuData) {
            let flatSubmenu = [];
            MenuData.forEach(menu => {
                menu.children.forEach(child => {
                    flatSubmenu.push(child)
                })
            })

            return flatSubmenu;
        })(MenuData);
        
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
    <div id="layer-tools" style="display: none;">
        <ul class="nav nav-tabs" id="tab-calculator">
            <li class="nav-item" role="wrapper-workable">
                <a class="nav-link active" aria-current="page" href="javascript:;">n进制转化</a>
            </li>
            <li class="nav-item" role="wrapper-calculator">
                <a class="nav-link" href="javascript:;">计算器</a>
            </li>
        </ul>
        <div class="tool_content wrapper item-tab-content" id="wrapper-workable">
            <div>支持在2~36进制之间进行任意转换，支持浮点型</div>
            <table>
                <tbody>
                    <tr>
                        <td>
                            <div class="content_area" id="input_area">
                                <ul>
                                    <li><label class="radio"><input type="radio" name="input_" value="2">2进制</label></li>
                                    <li><label class="radio"><input type="radio" name="input_" value="4">4进制</label></li>
                                    <li><label class="radio"><input type="radio" name="input_" value="8">8进制</label></li>
                                    <li><label class="radio"><input type="radio" name="input_" value="10" checked="checked">10进制</label></li>
                                    <li><label class="radio"><input type="radio" name="input_" value="16">16进制</label></li>
                                    <li><label class="radio"><input type="radio" name="input_" value="32">32进制</label></li>
                                    <li><select id="input_num" class="input-small">
                                            <option value="2">2进制</option>
                                            <option value="3">3进制</option>
                                            <option value="4">4进制</option>
                                            <option value="5">5进制</option>
                                            <option value="6">6进制</option>
                                            <option value="7">7进制</option>
                                            <option value="8">8进制</option>
                                            <option value="9">9进制</option>
                                            <option value="10" selected="">10进制</option>
                                            <option value="11">11进制</option>
                                            <option value="12">12进制</option>
                                            <option value="13">13进制</option>
                                            <option value="14">14进制</option>
                                            <option value="15">15进制</option>
                                            <option value="16">16进制</option>
                                            <option value="17">17进制</option>
                                            <option value="18">18进制</option>
                                            <option value="19">19进制</option>
                                            <option value="20">20进制</option>
                                            <option value="21">21进制</option>
                                            <option value="22">22进制</option>
                                            <option value="23">23进制</option>
                                            <option value="24">24进制</option>
                                            <option value="25">25进制</option>
                                            <option value="26">26进制</option>
                                            <option value="27">27进制</option>
                                            <option value="28">28进制</option>
                                            <option value="29">29进制</option>
                                            <option value="30">30进制</option>
                                            <option value="31">31进制</option>
                                            <option value="32">32进制</option>
                                            <option value="33">33进制</option>
                                            <option value="34">34进制</option>
                                            <option value="35">35进制</option>
                                            <option value="36">36进制</option>
                                        </select></li>
                                </ul>
                                <div class="input-prepend">
                                    <input id="input_value" type="text" value="" onpropertychange="px()" onchange="px()" oninput="px()" class="toolInput num_value" placeholder="在此输入待转换数字" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="content_area" id="output_area">
                                <ul>
                                    <li><label class="radio"><input type="radio" name="output_" value="2">2进制</label></li>
                                    <li><label class="radio"><input type="radio" name="output_" value="4">4进制</label></li>
                                    <li><label class="radio"><input type="radio" name="output_" value="8">8进制</label></li>
                                    <li><label class="radio"><input type="radio" name="output_" value="10">10进制</label></li>
                                    <li><label class="radio"><input type="radio" name="output_" value="16" checked="checked">16进制</label></li>
                                    <li><label class="radio"><input type="radio" name="output_" value="32">32进制</label></li>
                                    <li><select id="output_num" onchange="px(1);" class="input-small">
                                            <option value="2">2进制</option>
                                            <option value="3">3进制</option>
                                            <option value="4">4进制</option>
                                            <option value="5">5进制</option>
                                            <option value="6">6进制</option>
                                            <option value="7">7进制</option>
                                            <option value="8">8进制</option>
                                            <option value="9">9进制</option>
                                            <option value="10">10进制</option>
                                            <option value="11">11进制</option>
                                            <option value="12">12进制</option>
                                            <option value="13">13进制</option>
                                            <option value="14">14进制</option>
                                            <option value="15">15进制</option>
                                            <option value="16" selected="">16进制</option>
                                            <option value="17">17进制</option>
                                            <option value="18">18进制</option>
                                            <option value="19">19进制</option>
                                            <option value="20">20进制</option>
                                            <option value="21">21进制</option>
                                            <option value="22">22进制</option>
                                            <option value="23">23进制</option>
                                            <option value="24">24进制</option>
                                            <option value="25">25进制</option>
                                            <option value="26">26进制</option>
                                            <option value="27">27进制</option>
                                            <option value="28">28进制</option>
                                            <option value="29">29进制</option>
                                            <option value="30">30进制</option>
                                            <option value="31">31进制</option>
                                            <option value="32">32进制</option>
                                            <option value="33">33进制</option>
                                            <option value="34">34进制</option>
                                            <option value="35">35进制</option>
                                            <option value="36">36进制</option>
                                        </select></li>
                                </ul>
                                <div class="input-prepend">
                                    <input type="text" id="output_value" class="toolInput num_value" placeholder="转换结果" />
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-body wrapper-calculator item-tab-content" id="wrapper-calculator">
            <!--标准型-->
            <div class="standard-main calculator" id="std-main">
                <div class="title">
                    &nbsp;&nbsp;计算器
                </div>
                <!--结果显示区域-->
                <div class="result">
                    <!--显示类型信息-->
                    <div class="type" id="std-show-bar">
                        ☰&nbsp;&nbsp;&nbsp;标准计算器
                    </div>
                    <!--上一步的结果-->
                    <div class="pre" id="std-pre-step">
                        &nbsp;
                    </div>
                    <!--第二个/运算结果-->
                    <div class="second" id="std-show-input">0</div>
                </div>
                <ul id="std-top-symbol">
                    <li value="17">%</li>
                    <li value="18">√</li>
                    <li value="19"><img src="static/img/calculator/x_2.png" style="height: 18px;"></li>
                    <li value="20"><img src="static/img/calculator/1_x.png"></li>
                </ul>
                <!--数字和符号-->
                <ul id="std-num-symbol">
                    <li value="37">CE</li>
                    <li value="38">C</li>
                    <li value="39">Back</li>
                    <li value="16">÷</li>
                    <li value="7">7</li>
                    <li value="8">8</li>
                    <li value="9">9</li>
                    <li value="15">×</li>
                    <li value="4">4</li>
                    <li value="5">5</li>
                    <li value="6">6</li>
                    <li value="14">-</li>
                    <li value="1">1</li>
                    <li value="2">2</li>
                    <li value="3">3</li>
                    <li value="13">+</li>
                    <li value="11">±</li>
                    <li value="0">0</li>
                    <li value="10">.</li>
                    <li value="12">=</li>
                </ul>
                <!--侧边栏，选择计算器类型-->
                <ul class="type-bar" id="std-type-bar">
                    <li class="active">标准</li>
                    <li value="2">科学</li>
                    <li value="3">程序员</li>
                </ul>
            </div>
            <!--科学型-->
            <div class="science-main calculator" id="sci-main">
                <div class="title">
                    &nbsp;&nbsp;计算器
                </div>
                <!--结果显示区域-->
                <div class="sci-result">
                    <!--显示类型信息-->
                    <div class="type" id="sci-show-bar">
                        ☰&nbsp;&nbsp;&nbsp;科学计算器
                    </div>
                    <!--上一步的结果-->
                    <div class="pre" id="sci-pre-step">
                        &nbsp;
                    </div>
                    <!--第二个/运算结果-->
                    <div class="second" id="sci-show-input">0</div>
                </div>
                <!--上面的3行运算符-->
                <ul id="sci-top-symbol">
                    <li value="21">(</li>
                    <li value="22">)</li>
                    <li value="23"><img src="static/img/calculator/x_y_sqrt.png" style="height: 18px;width: 22px;">
                    </li>
                    <li value="24">n!</li>
                    <li value="25">Exp</li>
                    <li value="19"><img src="static/img/calculator/x_2.png" style="height: 18px;"></li>
                    <li value="26"><img src="static/img/calculator/x_y.png" style="height: 18px;"></li>
                    <li value="27">sin</li>
                    <li value="28">cos</li>
                    <li value="29">tan</li>
                    <li value="30"><img src="static/img/calculator/10_x.png"></li>
                    <li value="31">log</li>
                    <li value="32">sinh</li>
                    <li value="33">cosh</li>
                    <li value="34">tanh</li>
                </ul>
                <!--数字和符号-->
                <ul id="sci-num-symbol">
                    <li value="35">π</li>
                    <li value="37">CE</li>
                    <li value="38">C</li>
                    <li value="39">Back</li>
                    <li value="16">÷</li>
                    <li value="18">√</li>
                    <li value="7" class="number">7</li>
                    <li value="8" class="number">8</li>
                    <li value="9" class="number">9</li>
                    <li value="15">×</li>
                    <li value="17">%</li>
                    <li value="4" class="number">4</li>
                    <li value="5" class="number">5</li>
                    <li value="6" class="number">6</li>
                    <li value="14">-</li>
                    <li value="20"><img src="static/img/calculator/1_x.png"></li>
                    <li value="1" class="number">1</li>
                    <li value="2" class="number">2</li>
                    <li value="3" class="number">3</li>
                    <li value="13">+</li>
                    <li value="36">↑</li>
                    <li value="11">±</li>
                    <li value="0" class="number">0</li>
                    <li value="10">.</li>
                    <li value="12">=</li>
                </ul>
                <!--侧边栏，选择计算器类型-->
                <ul class="type-bar" id="sci-type-bar">
                    <li value="1">标准</li>
                    <li class="active">科学</li>
                    <li value="3">程序员</li>
                </ul>
            </div>
            <!--程序员型-->
            <div class="programmer-main calculator" id="pro-main">
                <div class="title">
                    &nbsp;&nbsp;计算器
                </div>
                <!--结果显示区域-->
                <div class="pro-result">
                    <!--显示类型信息-->
                    <div class="type" id="pro-show-bar">
                        ☰&nbsp;&nbsp;&nbsp;程序员计算器
                    </div>
                    <!--上一步的结果-->
                    <div class="pre" id="pro-pre-step">
                        &nbsp;
                    </div>
                    <!--第二个/运算结果-->
                    <div class="second" id="pro-show-input">0</div>
                    <!--显示16、10、8、2进制的值-->
                    <div id="pro-scales">
                        <div scale="16">十六进制(HEX)&nbsp;&nbsp;&nbsp;<span>0</span></div>
                        <div scale="10" class="scale-active">十进制(DEC)&nbsp;&nbsp;&nbsp;<span>0</span></div>
                        <div scale="8">八进制(OCT)&nbsp;&nbsp;&nbsp;<span>0</span></div>
                        <div scale="2">二进制(BIN)&nbsp;&nbsp;&nbsp;&nbsp;<span>0</span></div>
                    </div>
                </div>
                <!--上面的一行十六进制数字，因为默认是10进制，所以这些按钮默认禁用-->
                <ul id="pro-top-symbol">
                    <li class="disable-btn" value="40">A</li>
                    <li class="disable-btn" value="41">B</li>
                    <li class="disable-btn" value="42">C</li>
                    <li class="disable-btn" value="43">D</li>
                    <li class="disable-btn" value="44">E</li>
                    <li class="disable-btn" value="45">F</li>
                </ul>
                <!--数字和符号-->
                <ul id="pro-num-symbol">
                    <li value="36">↑</li>
                    <li value="37">CE</li>
                    <li value="38">C</li>
                    <li value="39">Back</li>
                    <li value="16">÷</li>
                    <li value="46">And</li>
                    <li value="7" class="number" bin-disable="1">7</li>
                    <li value="8" class="number" oct-disable="1" bin-disable="1">8</li>
                    <li value="9" class="number" oct-disable="1" bin-disable="1">9</li>
                    <li value="15">×</li>
                    <li value="47">Or</li>
                    <li value="4" class="number" bin-disable="1">4</li>
                    <li value="5" class="number" bin-disable="1">5</li>
                    <li value="6" class="number" bin-disable="1">6</li>
                    <li value="14">-</li>
                    <li value="48">Not</li>
                    <li value="1" class="number">1</li>
                    <li value="2" class="number" bin-disable="1">2</li>
                    <li value="3" class="number" bin-disable="1">3</li>
                    <li value="13">+</li>
                    <li value="21">(</li>
                    <li value="22">)</li>
                    <li value="0" class="number">0</li>
                    <li value="10" class="disable-btn" id="pro-point">.</li>
                    <li value="12">=</li>
                </ul>
                <!--侧边栏，选择计算器类型-->
                <ul class="type-bar" id="pro-type-bar">
                    <li value="1">标准</li>
                    <li value="2">科学</li>
                    <li class="active">程序员</li>
                </ul>
            </div>
        </div>
    </div>

    <div id="layer-search" style="display: none;">
        <div class="form-group suggest-wrap suggest-search">
            <div id="suggest-wrapper"></div>
            <p class="hints">↑ ↓ to navigate, ↵ to Jump</p>
        </div>
    </div>
</body>
</html>
