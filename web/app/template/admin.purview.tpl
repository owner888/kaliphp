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
                        <span class="icon"><a href="javascript:history.back(-1)"><i class="fa fa-chevron-left"></i>返回</a></span>
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <h5>基本信息</h5>
                    </div>
                    <div class="widget-content">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户名 :</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"> <{$info.username}></p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户组 :</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                <{if !empty($info.groupname)}>
                                <{$info.groupname}>
                                <{else}>
                                <font color="red">不属于任何组</font>
                                <{/if}>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="widget-title">
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <h5>权限管理</h5>
                    </div>
                    <div class="widget-content">

                        <{if '*' == $info.purviews}>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">权限:</label>
                            <div class="col-sm-10 ">
                                <p class="form-control-static">
                                    <!--此用户拥有至高无上的权限 ^_^!-->
                                    此用户法力无边 ^_^!
                                </p>
                            </div>
                        </div>

                        <{else}>

                        <div id="checkbox-group"></div>

                        <{/if}>

                        <{if '*' != $info.purviews}>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="submit" class="btn btn-success">提交</button>
                            </div>
                        </div>
                        <{/if}>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<{if '*' != $info.purviews}>
<script>
    var MenuData = <{$purviews}>;
    var MenuDataCheck = "<{$info.purviews}>";
</script>
<{/if}>
<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/plugins/validate/jquery.validate.min.js"></script>
<script src="static/frame/js/plugins/validate/messages_zh.min.js"></script>
<script src="static/frame/js/validate.js"></script>
<script src="static/frame/js/main.js"></script>
</body>
</html>
