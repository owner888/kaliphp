<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
    <style>
    div.uploader {
        background-image:none;
    }
    #queue {
        border: 1px solid #E5E5E5;
        height: 177px;
        overflow: auto;
        margin-bottom: 10px;
        padding: 0 3px 3px;
        width: 300px;
    }
</style>

</head>

<body>
<!--<embed type="application/x-shockwave-flash" src="static/uploadify/uploadify.swf" height="190" width="262" wmode="opaque"> </embed>-->
<div id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="btn-outline-wrap">
                <a href="javascript:history.back(-1)" class="btn btn-success btn-outline"><i class="fa fa-chevron-left"></i>返回</a>
                <!--<a href="?ct=filemanage&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>文件列表</a>-->
                <!--<a href="?ct=filemanage&ac=add" class="btn btn-info"><i class="fa fa-plus-circle"></i>文件添加</a>-->
                <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>垃圾桶</a>-->
            </div>

            <div class="widget-box">
                <form class="form-horizontal" id="validateForm" novalidate="novalidate" action="" method="POST">

                    <{form_token}>
                    <div class="widget-title">
                        <span class="icon"> <i class="fa fa-align-justify"></i> </span>
                        <!--<h5>基本信息</h5>-->
                    </div>
                    <div class="widget-filemanage">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">文件:</label>
                            <div class="col-sm-10">
                                <div id="queue"></div>
                                <input id="file_upload" name="file_upload" type="file" multiple="true" />
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<{include file='common/footer.tpl'}>
<script type="text/javascript">
    $(function() {
        $('#file_upload').uploadify({
            'auto'            : true,
            'buttonText'      : "选择文件", 
            'width'           : "",
            'height'          : "",
            'formData'        : {
                                    'timestamp' : '<{$timestamp}>',
                                    'token'     : '<{$token}>',
                                    'randname'  : '1',              // 是否随机生成文件名
                                    'tmp_path'  : 'file',           // 上传文件目录
                                },
            'swf'             : "static/uploadify/uploadify.swf",
            'uploader'        : "?ct=upload&ac=uploadify",
            'queueID'         : "queue",
            'onUploadSuccess' : function(file, data, response) { 
                //data = JSON.parse(data);
                //$("#pkgfile").val(data.filename);
                //$(".file_display").html("<font color='blue'>"+file.name+" 上传成功~</font>");
                //$(".file_display").show();
            }
        });
    });
</script>
</body>
</html>

