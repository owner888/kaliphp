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
            <!--<div class="btn-outline-wrap">-->
                <!--<a href="javascript:history.back(-1)" class="btn btn-success btn-outline"><i class="fa fa-chevron-left"></i>返回</a>-->
                <!--<a href="?ct=admin&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>用户列表</a>-->
                <!--<a href="?ct=admin&ac=add" class="btn btn-info"><i class="fa fa-plus-circle"></i>用户添加</a>-->
                <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>垃圾桶</a>-->
            <!--</div>-->

            <div class="widget-box">
                <form class="form-horizontal" id="validateForm" novalidate="novalidate" action="" method="POST">

                    <{form_token}>

                    <div class="widget-title">
                        <span class="icon"><a href="javascript:history.back(-1)"><i class="fa fa-chevron-left"></i>返回</a></span>
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <!--<h5>基本信息</h5>-->
                    </div>
                    <div class="widget-content">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 用户名 :</label>
                            <div class="col-sm-10">
                                <input name="username" type="text" class="form-control" datatype="*" nullmsg="请输入用户名" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 用户密码:</label>
                            <div class="col-sm-10">
                                <input type='password' name='password' class="form-control" datatype="password" nullmsg="请输入用户密码" errmsg="请输入大于6位，并且包含大小写字母和数字的密码" />
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 必须大于6位，包含大小写字母和数字</span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 真实姓名:</label>
                            <div class="col-sm-10">
                                <input type="text" name='realname' class="form-control" datatype="*" nullmsg="请输入真实姓名"/>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email:</label>
                            <div class="col-sm-10">
                                <input type="text" name='email' class="form-control" datatype="e" ignore="ignore" errmsg="请输入正确的邮箱" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">信任IP:</label>
                            <div class="col-sm-10">
                                <input type="text" name='safe_ips' class="form-control" />
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 多个IP以逗号分隔，一旦添加信任IP，用户就只能在信任IP环境下进行登陆和操作</span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">登录时长:</label>
                            <div class="col-sm-10">
                                <input type="text" name='session_expire' class="form-control" value="1440" />
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 以秒为单位，在此时间内用户无操作会自动退出登录，默认1440秒，即24分钟</span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户组:</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                <{foreach from=$group_options key=k item=v}>
                                <{if $k != '0'}>
                                <label><input type='checkbox' name='groups[]' value='<{$k}>' /> <{$v}></label>
                                <{/if}>
                                <{/foreach}>
                                </div>
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 一旦选择了超级管理员，就等于有了至高无上的荣誉，请慎重考虑</span>
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

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/validform.js"></script>
<script src="static/frame/js/newvalidform.js"></script>
<script src="static/frame/js/main.js"></script>

</body>
</html>
