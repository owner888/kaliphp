<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <title>登录到 <{$title}></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- 移动设备 viewport -->
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no,minimal-ui">
    <!-- 360浏览器默认使用Webkit内核 -->
    <meta name="renderer" content="webkit">
    <!-- 禁止搜索引擎抓取 -->
    <meta name="robots" content="nofollow">
    <!-- 禁止百度SiteAPP转码 -->
    <meta http-equiv="Cache-Control" content="no-siteapp">

    <!-- 样式 -->
    <link rel="stylesheet" href="static/frame/css/bootstrap.min14ed.css">
    <link rel="stylesheet" href="static/frame/css/font-awesome.min93e3.css">
    <link rel="stylesheet" href="static/frame/css/select2.css">
    <link rel="stylesheet" href="static/frame/css/main.css">
    <link rel="stylesheet" href="static/frame/css/login.css">
</head>
<body class="page-login layout-full page-dark">
    <div class="page height-full">
        <div class="page-content height-full">
            <div class="page-brand-info vertical-align animation-slide-left hidden-xs">
                <div class="page-brand vertical-align-middle">
                    <div class="brand">
                        <img class="brand-img" src="static/frame/img/logo-white-min.svg" height="50" />
                        <span><{$title}></span>
                    </div>
                    <!--<h2 class="hidden-sm">Admui 通用管理系统快速开发框架</h2>-->
                    <!--<ul class="list-icons hidden-sm">-->
                        <!--<li>-->
                            <!--<i class="fa fa-check" aria-hidden="true"></i> Admui 是一个基于最新 Web 技术的企业级通用管理系统快速开发框架，可以帮助企业极大的提高工作效率，节省开发成本，提升品牌形象。-->
                        <!--</li>-->
                        <!--<li>-->
                            <!--<i class="fa fa-check" aria-hidden="true"></i> 您可以 Admui 为基础，快速开发各种MIS系统，如CMS、OA、CRM、ERP、POS等。</li>-->
                        <!--<li>-->
                            <!--<i class="fa fa-check" aria-hidden="true"></i> Admui 紧贴业务特性，涵盖了大量的常用组件和基础功能，最大程度上帮助企业节省时间成本和费用开支。-->
                        <!--</li>-->
                    <!--</ul>-->
                    <!--<div class="hidden-sm">-->
                        <!--<a href="#" class="btn btn-primary m-r-5" target="_blank">-->
                            <!--<i class="fa fa-home"></i> 返回官网</a>-->
                        <!--<div class="btn-group m-r-5">-->
                            <!--<button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-expanded="false">-->
                                <!--<i class="fa fa-download"></i> 下载桌面版-->
                                <!--<span class="caret"></span>-->
                            <!--</button>-->
                            <!--<ul class="dropdown-menu dropdown-menu-success bullet" aria-labelledby="demoApp" role="menu">-->
                                <!--<li role="presentation">-->
                                    <!--<a href="#" role="menuitem">-->
                                        <!--<i class="fa fa-windows"></i> Windows 版</a>-->
                                <!--</li>-->
                                <!--<li role="presentation">-->
                                    <!--<a href="#" role="menuitem">-->
                                        <!--<i class="fa fa-apple"></i> MacOS 版</a>-->
                                <!--</li>-->
                            <!--</ul>-->
                        <!--</div>-->
                        <!--<a href="javascript:;" class="btn btn-info open-kf">-->
                            <!--<i class="fa fa-user"></i> 联系客服</a>-->
                    <!--</div>-->
                </div>
            </div>
            <div class="page-login-main animation-fade">
                
                <!-- start 表单错误提示 -->
                <div class="alert alert-danger alert-dismissible hide-tip"   role="alert" id="error-tip" ></div>
                <!-- end 表单错误提示 -->
                <div class="vertical-align">
                    <{if $request.ac == 'login'}>

                    <div class="vertical-align-middle" >
                        <div class="brand visible-xs text-center">
                            <img class="brand-img" src="static/frame/img/logo.svg" height="50" />
                            <span><{$title}></span>
                        </div>
                        <h3 class="hidden-xs">
                            登录 <{$title}>
                            <!--<div class="btn-group version-toggle">-->
                                <!--<a href="javascript:;" class="btn btn-default btn-outline btn-sm">pjax版</a>-->
                                <!--<a href="javascript:;" class="btn btn-default btn-outline btn-sm active">iframe版</a>-->
                                <!--<a href="javascript:;" class="btn btn-default btn-outline btn-sm ">basic版</a>-->
                            <!--</div>-->
                        </h3>
                        <!--<p class="hidden-xs">为了您的账号安全，首次登录时请修改初始密码。</p>-->
                        <form action="" class="login-form" method="post" id="loginForm">
                            <{form_token}>
                            <!--<div class="form-group">-->
                                <!--<label class="sr-only" for="identity">选择身份</label>-->
                                <!--<select class="form-control" id="identity" data-plugin="select2">-->
                                    <!--<option value="0">我自己</option>-->
                                    <!--<option value="wuliaokaka8@gmail" data-password="123456">王佳琪</option>-->
                                <!--</select>-->
                            <!--</div>-->
                            <div class="form-group">
                                <label class="sr-only" for="username">用户名</label>
                                <input type="text" class="form-control" id="username" name="username" value="<{$username}>" placeholder="请输入用户名"  datatype="*" nullmsg="用户名不能为空">
                            </div>
                            <div class="form-group form-password">
                                <label class="sr-only" for="password">密码</label>
                                <input type="password" class="form-control" id="password" name="password" value="<{$password}>" placeholder="请输入密码"  datatype="*6-18" nullmsg="密码不能为空" errmsg="">
                                <i class="fa fa-eye eye-btn" onclick="changePassword(this)"></i>
                            </div>
                            <{if $image_code}>
                            <div class="form-group">
                                <label class="sr-only" for="password">验证码</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="validate" placeholder="请输入验证码"  datatype="*" nullmsg="验证码不能为空" />
                                    <a class="input-group-addon nopadding reload-vify" href="javascript:;">
                                        <img style="display:none" height="32" />
                                        <span class="vify-wrap">点击获取验证码</span>
                                    </a>
                                </div>
                            </div>
                            <{/if}>
                            <div class="form-group clearfix">
                                <div class="checkbox-custom checkbox-inline checkbox-primary pull-left">
                                    <input type="checkbox" id="remember" name="remember" value="1" <{if $remember=='1'}>checked<{/if}> />
                                    <label for="remember">自动登录</label>
                                </div>
                                <a class="pull-right collapsed" data-toggle="collapse" href="#forgetPassword" aria-expanded="false" aria-controls="forgetPassword">
                                    忘记密码了？
                                </a>
                            </div>
                            <div class="collapse" id="forgetPassword" aria-expanded="true">
                                <div class="alert alert-warning alert-dismissible" role="alert">
                                    请联系管理员重置密码。
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt36">立即登录</button>

                            <{if $third_login}>
                            <div class="login-auth m-t">
                                <span>使用集团账号登录：</span>
                                <a href=""><img src="static/frame/img/h-icon.svg" alt="" /></a>
                                <a href=""><img src="static/frame/img/t-icon.svg" alt="" /></a>
                            </div>
                            <{/if}>
                        </form>
                    </div>

                    <{elseif $request.ac == 'reset_pwd'}>

                    <div class="vertical-align-middle" >
                        <div class="brand visible-xs text-center">
                            <img class="brand-img" src="static/frame/img/logo.svg" height="50" />
                            <span>修改初始密码</span>
                        </div>
                        <h3 class="hidden-xs">
                            修改初始密码
                        </h3>
                        <p class="hidden-xs">为了您的账号安全，首次登录时请修改初始密码。</p>
                        <form action="" class="login-form" method="post" id="loginForm">
                            <{form_token}>

                            <div class="form-group form-password">
                                <label class="sr-only" for="username">设置密码</label>
                                <input type="password" class="form-control" id="password" name="password" value="<{$password}>" placeholder="设置密码" datatype="password" nullmsg="密码不能为空" errmsg="必须包含数字，大小写字母的6-18位的密码！" />
                                <i class="fa fa-eye eye-btn" onclick="changePassword(this)"></i>
                            </div>
                            <div class="form-group form-password">
                                <label class="sr-only" for="password">确认密码</label>
                                <input type="password" class="form-control" id="confpass" name="confpass" value="<{$confpass}>" placeholder="确认密码" datatype="*" nullmsg="确认密码不能为空" errmsg="两次输入密码不一致"  recheck="password"/>
                                <i class="fa fa-eye eye-btn" onclick="changePassword(this)"></i>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt36">立即修改</button>
                        </form>
                    </div>

                    <{else}>

                    <div class="approve-wrap vertical-align-middle" >
                        <div class="brand visible-xs text-center">
                            <img class="brand-img" src="static/frame/img/logo.svg" height="50" />
                            <span>MFA认证</span>
                        </div>
                        <h3 class="hidden-xs">
                            MFA认证
                        </h3>
                        <!--<p class="hidden-xs">账号保护已开启，请根据提示完成以下操作</p>-->
                        <form action="" class="login-form" method="post" id="approveForm">
                            <{form_token}>
                            <h6 class="m-t">账号保护已开启，请根据提示完成以下操作</h6>
                            <img src="static/frame/img/otp_auth.png" alt="" />
                            <p class="m-b-lg"> 请打开手机Google Authenticator应用，输入6位动态码</p>
                            <div class="form-group">
                                <label class="sr-only" for="username">用户名</label>
                                <input type="number" class="form-control" id="number" name="otp_code" value="" placeholder="6位数字" datatype="*" nullmsg="动态密码不能为空" />
                            </div>

                            <button type="submit" class="btn btn-primary btn-block m-t">下一步</button>

                            <div class="m-t">
                                <a href="#">
                                    <small>如果不能提供MFA验证码，请联系管理员!</small>
                                </a>
                            </div>
                        </form>
                    </div>

                    <{/if}>
                </div>
                <footer class="page-copyright">
                    <p>傲创网络科技 &copy;
                        <a href="#" target="_blank">ultron.com</a>
                    </p>
                </footer>
            </div>
        </div>
    </div>

    <!-- JS -->
    <script src="static/frame/js/jquery.min.js"></script>
    <script src="static/frame/js/bootstrap.min.js"></script>
    <script src="static/frame/js/select2.min.js"></script>
    <script src="static/frame/js/validform.js"></script>
    <script src="static/frame/js/newvalidform.js"></script>
    <script src="static/frame/js/main.js"></script>
    <script language="javascript">
    if (top !== self) {
        top.location.href = location.href;
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


    $(".login-form").Validform({
        tiptype:function(msg,o,cssctl){
            if(o.type==3){
                $(o.obj).addClass().closest("div.form-group").addClass("has-error");
                var str = '<span class="Validform_checktip Validform_wrong"><i class="fa fa-times-circle"></i> '+ msg+'</span>';
                $(o.obj).closest("div.form-group").children(".Validform_checktip").remove();
                $(o.obj).closest("div.form-group").append(str);
                $(o.obj).parents("div.form-group").find(".control-label").css("color","#ed5565");

            }else{
                $(o.obj).parents("div.form-group").find(".control-label").css("color","#1ab394");
                $(o.obj).closest("div.form-group").removeClass("has-error").addClass("has-success");
                if($(o.obj).attr("sucmsg")){
                    $(o.obj).closest("div.form-group").children(".Validform_checktip").addClass("Validform_right").html('<i class="fa fa-check-circle-o"></i> '+$(o.obj).attr("sucmsg"));
                }else{
                    $(o.obj).closest("div.form-group").children(".Validform_checktip").remove();
                }

            }
        },
        showAllError:true,
        datatype: { //自定义方法
            "password": passwordFn,
            
        }
    });

    //tab toggle
    $('.version-toggle').on('click','a',function(){
        $(this).addClass('active').siblings().removeClass('active');
    });

    //vify
    $('.reload-vify').click(function(){
        hideError();
        if($(this).hasClass('loading')) return false;
        var link = $(this),
            img = link.children('img'),
            span = link.find('span');
            
        link.addClass('loading');
        span.text('加载中').show();
        img.hide();
        img.on('load',function(){
            img.show();
            span.hide();
            link.removeClass('loading');
        });
        img.on('error',function(e){
            img.hide();
            span.text('请重试').show();
            link.removeClass('loading');
        });
        img.attr('src','?ac=validate_image&t=' + Date.now())
    });

    //error tip
    function showError(msg){
        $("#error-tip").text(msg).fadeIn();
    }
    function hideError(){
        $("#error-tip").fadeOut();
    }

    //查看密码
    function changePassword(e){
        var _this = $(e),
            _input =_this.siblings("input");
            if(_input.attr("type")=="password"){
                _input.attr("type","text");
                _this.addClass("fa-eye-slash").removeClass("fa-eye");
            }else{
                _input.attr("type","password");
                _this.addClass("fa-eye").removeClass("fa-eye-slash");
            }
    }
    <{if !empty($errmsg)}>
        showError('<{$errmsg}>');
    <{/if}>
</script>
</body>
</html>
