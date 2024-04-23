<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="static/css/bootstrap.min14ed.css">
    <link rel="stylesheet" href="static/css/font-awesome.min93e3.css?v=4.4.0">
    <link rel="stylesheet" href="static/css/login.css?v=5.29">
</head>
<body class="page-login layout-full page-dark">
    <div class="page height-full">
        <div class="page-content height-full">
            <div class="page-brand-info vertical-align animation-slide-left hidden-xs">
                <div class="page-brand vertical-align-middle">
                    <div class="brand">
                        <img class="brand-img" src="static/img/logo-white-min.svg" height="50" />
                        <span><{$app_name}></span>
                    </div>
                </div>
            </div>
            <div class="page-login-main animation-fade">
                
                <!-- start 表单错误提示 -->
                <div class="alert alert-danger alert-dismissible hide-tip" role="alert" id="error-tip" ></div>
                <!-- end 表单错误提示 -->
                <div class="vertical-align">
                    <{if $request.ac == 'login'}>

                    <div class="vertical-align-middle" >
                        <div class="brand visible-xs text-center">
                            <img class="brand-img" src="static/img/logo.svg" height="50" />
                            <span><{$app_name}></span>
                        </div>
                        <h3 class="hidden-xs">
                            <{$app_name}>
                        </h3>
                        <p class="hidden-xs">为了您的账号安全，首次登录时请修改初始密码</p>
                        <form action="" class="login-form" method="post" id="loginForm">
                            <{form_token}>
                            <div class="form-group">
                                <label class="sr-only" for="username"><{lang key='username'}></label>
                                <input type="text" class="form-control" id="username" name="username" value="<{$username}>" placeholder="<{lang key='username'}>"  datatype="*" nullmsg="用户名不能为空">
                            </div>
                            <div class="form-group form-password">
                                <label class="sr-only" for="password"><{lang key='password'}></label>
                                <input type="password" class="form-control" id="password" name="password" value="<{$password}>" placeholder="<{lang key='password'}>"  datatype="*6-18" nullmsg="密码不能为空" errmsg="">
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
                                    <label for="remember"><{lang key='remember'}></label>
                                </div>
                                <a class="pull-right collapsed" data-toggle="collapse" href="#forgetPassword" aria-expanded="false" aria-controls="forgetPassword">
                                    <{lang key='forget_password'}>
                                </a>
                            </div>
                            <div class="collapse" id="forgetPassword" aria-expanded="true">
                                <div class="alert alert-warning alert-dismissible" role="alert">
                                    <{lang key='manage_reset_password'}>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt36"><{lang key='login'}></button>
                        </form>
                    </div>

                    <{elseif $request.ac == 'reset_pwd'}>

                    <div class="vertical-align-middle" >
                        <div class="brand visible-xs text-center">
                            <img class="brand-img" src="static/img/logo.svg" height="50" />
                            <span><{lang key='modify_inital_password'}></span>
                        </div>
                        <h3 class="hidden-xs">
                            <{lang key='modify_inital_password'}>
                        </h3>
                        <p class="hidden-xs login-tips"><{lang key='modify_inital_password_tips'}></p>
                        <form action="" class="login-form" method="post" id="loginForm">
                            <{form_token}>

                            <div class="form-group form-password">
                                <label class="sr-only" for="username"><{lang key='password'}></label>
                                <input type="password" class="form-control" id="password" name="password" value="<{$password}>" placeholder="<{lang key='password'}>" datatype="password" nullmsg="<{lang key='password'}> <{lang key='nullmsg_tips'}>" errmsg="<{lang key='password_errmsg_tips'}>" />
                                <i class="fa fa-eye eye-btn" onclick="changePassword(this)"></i>
                            </div>
                            <div class="form-group form-password">
                                <label class="sr-only" for="password"><{lang key='confirm_password'}></label>
                                <input type="password" class="form-control" id="confpass" name="confpass" value="<{$confpass}>" placeholder="<{lang key='confirm_password'}>" datatype="*" nullmsg="<{lang key='confirm_password'}> <{lang key='nullmsg_tips'}>" errmsg="<{lang key='confirm_password_errmsg_tips'}>" recheck="password" />
                                <i class="fa fa-eye eye-btn" onclick="changePassword(this)"></i>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block mt36"><{lang key='modify_now'}></button>
                        </form>
                    </div>

                    <{else}>

                    <div class="approve-wrap vertical-align-middle" >
                        <div class="brand visible-xs text-center">
                            <img class="brand-img" src="static/img/logo.svg" height="50" />
                            <span>MFA认证</span>
                        </div>
                        <h3 class="hidden-xs">
                            MFA认证
                        </h3>
                        <p class="hidden-xs">账号保护已开启，请根据提示完成以下操作</p>
                        <form action="" class="login-form" method="post" id="approveForm">
                            <{form_token}>
                            <h6 class="m-t">账号保护已开启，请根据提示完成以下操作</h6>
                            <img src="static/img/otp_auth.png" alt="" />
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
                    
                </footer>
            </div>
        </div>
    </div>
    
    <script src="static/js/jquery.min.js"></script>
    <script src="static/js/validform.js"></script>
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
