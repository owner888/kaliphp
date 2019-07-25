<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title><{$title}></title>
    <link href="static/frame/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="static/frame/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="static/frame/css/animate.min.css" rel="stylesheet">
    <link href="static/frame/css/main.css" rel="stylesheet">
    <script src="static/frame/js/jquery.min.js?v=2.1.4"></script>
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
                        <h5>
                            伪装密码
                            <small>伪装密码登陆时，私密页面会被隐藏</small>
                        </h5>
                    </div>
                    <div class="widget-content">

                        <div class="form-group form-password">
                            <label class="col-sm-2 control-label"><code>*</code> 伪装密码:</label>
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

                        <div class="form-group form-password">
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
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="submit" class="btn btn-primary">提交</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/validform.js"></script>
<script src="static/frame/js/newvalidform.js"></script>
<script src="static/frame/js/main.js"></script>

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
