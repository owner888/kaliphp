<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>
<body>
<div id="content">
    <div class="container-fluid">
        <div class="row">

            <div class="widget-box">
                <form class="form-horizontal" id="validateForm" novalidate="novalidate" action="" method="POST">

                    <{form_token}>

                    <div class="widget-title">
                        <span class="icon"> <i class="fa fa-lock"></i> </span>
                        <h5>修改密码</h5>
                    </div>
                    <div class="widget-content">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户名:</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"> <{$v.username}></p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group form-password">
                            <label class="col-sm-2 control-label"><code>*</code> 用户密码:</label>
                            <div class="col-sm-10">
                                <input type="password" id="password" name='password' class="form-control"
                                       datatype="password"
                                       nullmsg="请输入新密码"
                                        errmsg="请输入大于大于6位，包含大小写字母和数字的密码"/>
                                        <i class="fa fa-eye eye-btn" onclick="changePassword(this)"></i>
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 必须大于6位，包含大小写字母和数字</span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group  form-password">
                            <label class="col-sm-2 control-label"><code>*</code> 确认密码:</label>
                            <div class="col-sm-10">
                                <input type="password" name='passwordok' class="form-control"
                                       recheck="password"
                                       nullmsg="请再一次输入密码"
                                       datatype="*" errmsg="两次密码不符，请重新输入"/>
                                       <i class="fa fa-eye eye-btn" onclick="changePassword(this)"></i>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 昵称:</label>
                            <div class="col-sm-10">
                                <input type="text" name='realname' class="form-control" value="<{$v.realname}>" datatype="*" nullmsg="请输入昵称"/>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email:</label>
                            <div class="col-sm-10">
                                <input type="text" name="email" class="form-control" value="<{$v.email}>" datatype="e" ignore="ignore" errmsg="请输入正确的邮箱" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户组:</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"> <{$v.groupname}></p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">上次登录时间:</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"> <{$lastlogin.logintime|date_format:"%Y-%m-%d %H:%M:%S" }></p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">上次登录地址:</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">  <{$lastlogin.loginip}></p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">上次登录国家:</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">  <{$lastlogin.logincountry}></p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="submit" class="btn btn-success">提交</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<{include file='common/footer.tpl'}>
<script>
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
</script>
</body>
</html>
