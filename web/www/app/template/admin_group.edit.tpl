<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title><{$app_name}></title>
    <link href="static/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="static/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="static/css/animate.min.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">
    <script src="static/frame/js/jquery.min.js?v=2.1.4"></script>
</head>

<body>

<div id="content">
    <div class="container-fluid">
        <div class="row">
            <!--<div class="btn-outline-wrap">-->
                <!--<a href="javascript:history.back(-1)" class="btn btn-success btn-outline"><i class="fa fa-chevron-left"></i>返回</a>-->
                <!--<a href="?ct=admin_group&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>用户组列表</a>-->
                <!--<a href="?ct=admin_group&ac=add" class="btn btn-outline btn-info"><i class="fa fa-plus-circle"></i>用户组添加</a>-->
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
                            <label class="col-sm-2 control-label"><code>*</code> 用户组名 :</label>
                            <div class="col-sm-10">
                                <input name="name" type="text" class="form-control" datatype="*" nullmsg="请输入用户组名" value="<{$info.name}>" />
                            </div>
                        </div>
                    </div>

                    <div class="widget-title">
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <h5>权限管理</h5>
                    </div>
                    <div class="widget-content">

                        <div id="checkbox-group"></div>

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
<script>
    var MenuData = <{$purviews}>;
    var MenuDataCheck = "<{$info.purviews}>";
</script>
<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/validform.js"></script>
<script src="static/frame/js/newvalidform.js"></script>
<script src="static/frame/js/main.js"></script>
</body>
</html>
