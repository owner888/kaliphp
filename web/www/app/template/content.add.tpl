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
    <link href="static/redactor/css/redactor.css" rel="stylesheet" />
    <script src="static/frame/js/jquery.min.js?v=2.1.4"></script>

    <script src="static/webuploader/webuploader.js"></script>
    <script src="static/redactor/js/redactor.js"></script>
    <script src="static/redactor/plugins/webuploadImage.js"></script>
    <script src="static/redactor/plugins/webuploadVideo.js"></script>
    <script src="static/redactor/plugins/pasteImage.js"></script>
    <script src="static/redactor/js/zh_cn.js"></script>
    <script src="static/redactor/plugins/fullscreen.js"></script>
    <script src="static/redactor/plugins/fontcolor.min.js"></script>
    <script src="static/redactor/plugins/fonttotal.js"></script>

    <script type="text/javascript">
        $(function()
        {
            $('#redactor_content').redactor({
                //imageGetJson: '?ct=upload&ac=redactor&type=file_manager_json',
                imageWebUpload: '?ct=upload&ac=upload',
                imageUpload: '?ct=upload&ac=upload_html5',
                imageDir: 'image',
                thumbWidth: 0,
                thumbHeight: 0,
                // videoGetJson: '?ct=upload&ac=redactor&type=file_manager_json',
                videoUpload: '?ct=upload&ac=upload_chunked',
                videoDir: 'video',
                plugins: [ 'fullscreen', 'webuploadImage', 'webuploadVideo', 'pasteImage', 'fontcolor'],
                minHeight: '480px',
                maxHeight: '480px',
                lang: 'zh_cn',
                imgFileNumLimit: 3,
                videoFileNumLimit: 3,
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

                    <{form_token}>

                    <div class="widget-title">
                        <span class="icon"><a href="javascript:history.back(-1)"><i class="fa fa-chevron-left"></i>返回</a></span>
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <h5>
                            添加内容
                            <small>我是注释拉</small>
                        </h5>
                    </div>
                    <div class="widget-content">

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 标题:</label>
                            <div class="col-sm-10">
                                <input name="name" type="text" class="form-control" datatype="*" nullmsg="请输入标题" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 分类:</label>
                            <div class="col-sm-10">
                                <select name="catid" class="form-control" datatype="*" nullmsg="请输入分类">
                                    <{html_options options=$options selected=$catid}>       
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group uploader-group uploader-group-img" data-compress="true" data-thumb_w='100' data-auto="true" data-len="1" data-multiple="false" data-dir="image" data-extensions="gif,jpg,jpeg,bmp,png" data-chunked="true">
                            <label class="col-sm-2 control-label"> 单图:</label>
                            <div class="col-sm-10">
                                <!--用来存放文件信息-->
                                <div class="uploader-list"></div>
                                <a class="btn btn-dark uploader-picker" data-file="image" data-type="image"><i class="fa fa-upload"></i> </a>
                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="请上传文件">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <!--data-height="1026"-->
                        <!--data-width="1624"-->
                        <div class="form-group uploader-group uploader-group-img" 
                        data-auto="true" 
                        data-dir="image"
                        data-extensions="gif,jpg,jpeg,bmp,png">
                            <label class="col-sm-2 control-label">多图:</label>
                            <div class="col-sm-10">
                                <div class="uploader-list"></div>
                                <a class="btn btn-dark uploader-picker" data-file="images" data-type="image"><i class="fa fa-upload"></i> </a>
                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="请上传文件">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">内容:</label>
                            <div class="col-sm-10">
                                <div class="total-wrap" style="position: relative">
                                    <textarea id="redactor_content" name="content" cols="30" rows="10" class="form-control"></textarea>
                                </div>

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
<script src="static/frame/js/main.js<{$clear_cache}>"></script>
<script src="static/frame/js/validform.js"></script>
<script src="static/frame/js/newvalidform.js"></script>
<script src="static/frame/js/webuploader.own.js<{$clear_cache}>"></script>
</body>
</html>
