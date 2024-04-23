<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>

<body>

<div id="content">
    <div class="container-fluid">
        <div class="row">
            <!--<div class="btn-outline-wrap">-->
                <!--<a href="javascript:history.back(-1)" class="btn btn-success btn-outline"><i class="fa fa-chevron-left"></i>返回</a>-->
                <!--<a href="?ct=category&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>分类列表</a>-->
                <!--<a href="?ct=category&ac=add" class="btn btn-info"><i class="fa fa-plus-circle"></i>分类添加</a>-->
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
                            <div class="col-sm-12">
                                <input name="name" type="text" class="form-control"  />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div id="editormd">
                                    <textarea name="content" style="display:none;"></textarea>
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
<{include file='common/footer.tpl'}>
<script type="text/javascript">
    var testEditor;

    $(function() {
        testEditor = editormd("editormd", {
            width   : "100%",
            height  : 640,
            syncScrolling : "single",
            path    : "static/editormd/lib/",
            taskList            : true,
            saveHTMLToTextarea  : true,     // 保存 HTML 到 Textarea
            tocm                : true,     // Using [TOCM]
            tex                 : true,     // 开启科学公式TeX语言支持，默认关闭
            flowChart           : true,     // 开启流程图支持，默认关闭
            sequenceDiagram     : true,     // 开启时序/序列图支持，默认关闭,
            imageUpload         : true,
            imageFormats : ["jpg", "jpeg", "gif", "png", "bmp", "webp"],
            //imageUploadURL : "./php/upload.php",
            imageUploadURL : "?ct=upload&ac=upload",
            onload : function() {
                console.log('onload', this);
                //this.fullscreen();
                //this.unwatch();
                //this.watch().fullscreen();

                //this.setMarkdown("#PHP");
                //this.width("100%");
                //this.height(480);
                //this.resize("100%", 640);
            },
            toolbarIcons : function() {
                return ["undo", "redo", "|", "bold", "italic", "del", "code", "list-ul", "list-ol", "hr", "image", "link", "h1", "h2", "h3", "table"]
            }
        });
    });
</script>

</body>
</html>
