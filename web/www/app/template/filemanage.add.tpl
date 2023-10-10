<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><{$app_name}></title>
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
                <!--<a href="?ct=filemanage&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>文件列表</a>-->
                <!--<a href="?ct=filemanage&ac=add" class="btn btn-info"><i class="fa fa-plus-circle"></i>文件添加</a>-->
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
                    <div class="widget-filemanage widget-content">
                        <div class="form-group uploader-group">
                            <label class="col-sm-2 control-label"><code>*</code> 文件:</label>
                            <div class="col-sm-10">
                                <!--用来存放文件信息-->
                                <div class="uploader-list"></div>
                                <a class="btn btn-dark uploader-picker" data-file="file1"><i class="fa fa-upload"></i> 选择文件</a>
                                <button type="button" class="btn btn-dark uploader-btn" data-id="0">
                                    <i class="fa fa-upload"></i>
                                    开始上传
                                </button>
                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="请上传文件">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group uploader-group" data-multiple="true" data-auto="true" data-len="" data-size="2" data-maxlen="3">
                            <label class="col-sm-2 control-label"><code>*</code> 文件2:</label>
                            <div class="col-sm-10">
                                <div class="uploader-list"></div>
                                <a class="btn btn-dark uploader-picker" data-file="file2"><i class="fa fa-upload"></i> 自动上传</a>

                                <!--<button type="button" class="btn btn-dark uploader-btn" data-id="1">-->
                                    <!--<i class="fa fa-upload"></i>-->
                                    <!--开始上传-->
                                <!--</button>-->
                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="请上传文件">
                            </div>
                            llllll
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group uploader-group uploader-group-img" 
                            data-compress="true" 
                            data-auto="true" 
                            data-len="1" 
                            data-multiple="false" 
                            data-extensions="gif,jpg,jpeg,bmp,png">
                            <label class="col-sm-2 control-label"> 单图上传:</label>
                            <div class="col-sm-10">
                                <!--用来存放文件信息-->
                                <div class="uploader-list"></div>
                                <a class="btn btn-dark uploader-picker" data-file="image1" data-type="image"><i class="fa fa-upload"></i> 选择文件</a>
                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="请上传文件">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group uploader-group uploader-group-img"  
                            data-auto="true" data-extensions="gif,jpg,jpeg,bmp,png" data-chunked="true">
                            <label class="col-sm-2 control-label">多图上传:</label>
                            <div class="col-sm-10">
                                <div class="uploader-list"></div>
                                <a class="btn btn-dark uploader-picker" data-file="image2" data-type="image"><i class="fa fa-upload"></i> </a>
                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="请上传文件">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>

                        <div class="form-group uploader-group uploader-group-img" 
                            data-compress="true" 
                            data-auto="true" 
                            data-len="1" 
                            data-multiple="false" 
                            data-height="1389"
                            data-width="2560"
                            data-extensions="gif,jpg,jpeg,bmp,png">
                            <label class="col-sm-2 control-label"> 指定图片大小上传:</label>
                            <div class="col-sm-10">
                                <!--用来存放文件信息-->
                                <div class="uploader-list"></div>
                                <a class="btn btn-dark uploader-picker" data-file="image1" data-type="image"><i class="fa fa-upload"></i> 选择文件</a>
                            </div>
                            <div class="col-sm-9 col-sm-offset-2" >
                                <p class="help-block m-b-none m-t-xs"><i class="fa fa-info-circle"></i> 通过 data-height="1389" data-width="2560" </p>

                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="请上传文件">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        <div class="form-group uploader-group uploader-group-img" 
                            data-compress="true" 
                            data-auto="true" 
                            data-len="1" 
                            data-multiple="false" 
                            data-corp="true"
                            data-corp_width="120" 
                            data-corp_height="120" 
                            data-extensions="gif,jpg,jpeg,bmp,png">
                            <label class="col-sm-2 control-label"> 图片裁剪:</label>
                            <div class="col-sm-10">
                                <!--用来存放文件信息-->
                                <div class="uploader-list"></div>
                                <a class="btn btn-dark uploader-picker" data-file="image1" data-type="image"><i class="fa fa-upload"></i> 选择文件</a>
                                
                            </div>
                            <div class="col-sm-9 col-sm-offset-2" >
                                <p class="help-block m-b-none m-t-xs">
                                    <i class="fa fa-info-circle"></i> 
                                    data-corp="true"--是否裁剪；
                                    data-corp_width="120" -- 裁剪宽
                                    data-corp_height="120" -- 裁剪高
                                    如果高不传则安宽度自适应裁剪
                                </p>
                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="请上传文件">
                            </div>
                        </div>
                       
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
<script src="static/webuploader/webuploader.min.js"></script>
<script src="static/frame/js/webuploader.own.js<{$clear_cache}>"></script>
<script src="static/frame/js/main.js"></script>
<script src="static/frame/js/validform.js"></script>
<script src="static/frame/js/newvalidform.js"></script>
</body>
</html>

