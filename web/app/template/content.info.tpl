<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title><{$title}></title>
    <link href="static/frame/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="static/frame/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="static/frame/css/animate.min.css" rel="stylesheet">
    <link href="static/frame/css/main.css<{$clear_cache}>" rel="stylesheet">
    <link href="static/redactor/css/redactor.css" rel="stylesheet" />
    <script src="static/frame/js/jquery.min.js?v=2.1.4"></script>
    <script src="static/redactor/js/redactor.js"></script>
    <script src="static/redactor/js/zh_cn.js"></script>
    <script type="text/javascript">
        $(function()
        {
            $('#redactor_content').redactor({
                imageGetJson: '?ct=upload&ac=redactor&type=file_manager_json',
                imageUpload: '?ct=upload&ac=redactor&type=image',
                clipboardUploadUrl: '?ct=upload&ac=redactor&type=clipboard',
                lang: 'zh_cn',
                minHeight: '480', // pixels
                maxHeight: '480', // pixels
            });
        });
    </script>
</head>

<body>

<div id="content">
    <div class="container-fluid">
        <div class="row">
            <!--<div class="btn-outline-wrap">-->
                <!--<a href="javascript:history.back(-1)" class="btn btn-success btn-outline"><i class="fa fa-chevron-left"></i>返回</a>-->
                <!--<a href="?ct=content&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>内容列表</a>-->
                <!--<a href="?ct=content&ac=add" class="btn btn-info"><i class="fa fa-plus-circle"></i>内容添加</a>-->
                <!--<a href="?ct=content&ac=trash" class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>垃圾桶</a>-->
            <!--</div>-->

            <div class="widget-box">
                <form class="form-horizontal" id="validateForm" novalidate="novalidate" action="" method="POST">

                    <div class="widget-title">
                        <span class="icon"><a href="javascript:history.back(-1)"><i class="fa fa-chevron-left"></i>返回</a></span>
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <!--<h5>-->
                            <!--添加内容-->
                            <!--<small>我是注释拉</small>-->
                        <!--</h5>-->
                    </div>
                    <div class="widget-content">

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">标题:</label>
                                    <div class="col-sm-10">
                                        <p class="form-control-static"> <{$info.name}></p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">标题:</label>
                                    <div class="col-sm-10">
                                        <p class="form-control-static"> <{$info.name}></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">分类:</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"> <{$info.name}></p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">内容:</label>
                            <div class="col-sm-10">
                                <span class="form-control-static"> <{$info.content}></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/main.js<{$clear_cache}>"></script>

</body>
</html>
