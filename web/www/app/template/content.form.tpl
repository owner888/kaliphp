<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
    <link href="static/editor.md/css/editormd.min.css" rel="stylesheet">
    <link href="static/css/jsonmain.css" rel="stylesheet">
</head>

<body>
<div id="content">
    <div class="container-fluid">
        <div class="row">

            <div class="widget-box">
                <form class="form-horizontal" id="validateForm" novalidate="novalidate" action="" method="POST">

                    <{form_token}>

                    <div class="widget-title">
                        <span class="icon"><a href="javascript:history.back(-1)"><i class="fa fa-chevron-left"></i> <{lang key='common_back'}></a></span>
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <!--<h5>基本信息</h5>-->
                    </div>
                    <div class="widget-content">
                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> <{lang key='common_name'}>:</label>
                            <div class="col-sm-10">
                                <input name="name" type="text" class="form-control" value="<{$v.name}>" datatype="*" nullmsg="请输入标题"/>
                            </div>
                        </div>

                       <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> <{lang key='common_category'}>:</label>
                            <div class="col-sm-10">
                                <select name="catid" class="form-control" datatype="*" nullmsg="请输入分类">
                                    <{html_options options=$options selected=$v.catid}>       
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group uploader-group uploader-group-img"  
                            data-compress="true" 
                            data-thumb_w='100' 
                            data-auto="true" 
                            data-len="1" 
                            data-multiple="false" 
                            data-dir="image" 
                            data-extensions="gif,jpg,jpeg,bmp,png" 
                            data-chunked="true">
                            <label class="col-sm-2 control-label"> <{lang key='common_image'}>:</label>
                            <div class="col-sm-10">
                                <!--用来存放文件信息-->
                                <{if $action == "add"}>
                                <div class="uploader-list"></div>
                                <a class="btn btn-dark uploader-picker" data-file="image" data-type="image"><i class="fa fa-upload"></i> </a>
                                <{else}>
                                <div class="uploader-list">
                                    <div class="item img-item pull-left" style="margin-bottom:10px;margin-right:10px;">
                                        <img style="width:100px;height:100px;" src="<{$v.image.filelink}>" />
                                        <i class="fa fa-close close-btn"></i>
                                        <div class="progress progress-striped active" style="display: none;">
                                            <div class="progress-bar" role="progressbar" style="width: 100%;"></div>
                                        </div>
                                        <input type="hidden" value="<{$v.image.filename}>" name="image" class="hid-filename" />
                                    </div>
                                </div>
                                <a class="btn btn-dark uploader-picker hide" data-file="image" data-type="image"><i class="fa fa-upload"></i> </a>
                                <{/if}>
                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="Please Upload File">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group uploader-group uploader-group-img"  
                            data-auto="true" 
                            data-dir="image"
                            data-extensions="gif,jpg,jpeg,bmp,png">
                            <label class="col-sm-2 control-label"><code>*</code> Photo Gallery:</label>
                            <div class="col-sm-10">
                                <{if $action == "add"}>
                                <div class="uploader-list" id="list-sortable-gallery"></div>
                                <a class="btn btn-dark uploader-picker" data-file="images" data-type="image"><i class="fa fa-upload"></i> </a>
                                <{else}>
                                <div class="uploader-list" id="list-sortable-gallery">
                                    <{foreach from=$v.images item='image'}>
                                    <div class="item img-item pull-left items-gallery">
                                        <img src="<{$image.filelink}>" />
                                        <i class="fa fa-close close-btn"></i>
                                        <i class="fa fa-search btn-preview-photo" data-src="<{$image.filelink}>"></i>
                                        <input type="hidden" value="<{$image.filename}>" class="hid-filename item-hidFilename" />
                                        <{if isset($image.realname)}>
                                        <input type="hidden" value="{$image.realname}" class="item-realname" />
                                        <{else}>
                                        <input type="hidden" value="" class="item-realname" />
                                        <{/if}>
                                    </div>
                                    <{/foreach}>
                                    <input type="hidden" value="" name="images" class="hid-filename list" />
                                    <input type="hidden" value="" name="realnames" class="hid-filename list-realname" />
                                </div>
                                <a class="btn btn-dark uploader-picker" data-file="images" data-type="image"><i class="fa fa-upload"></i> </a>
                                <{/if}>
                            </div>
                            <div class="hidden-input col-sm-9 col-sm-offset-2">
                                <input type="hidden" class="form-control file" datatype="file" nullmsg="Please Upload File">
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><{lang key='common_content'}>:</label>
                            <div class="col-sm-10">
                                <div class="total-wrap" style="position: relative" id="redactor_content">
                                    <textarea id="editor-con" name="content" style="display: none;" class="form-control"><{$v.content}></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">XML Content:</label>
                            <div class="col-sm-10">
                                <div class="total-wrap" style="z-index: 1;" id="xml-content">
                                    <textarea id="editor-xml" name="content" style="display: none;" class="form-control"><{$v.content}></textarea>
                                </div>

                                <div>
                                    <button type="button" id="defaultaction" class="btn btn-dark" onclick="validateXML()">
                                        XML验证
                                    </button>
                                    <button type="button" class="btn btn-dark" onclick="xmlTreeView()">
                                        XML查看
                                    </button>
                                    <button type="button" class="btn btn-dark" title="Beautify XML">
                                        XML格式化/美化
                                    </button>
                                </div>

                                <div class="mod-jsoneditor">
                                    <div id="output-json" tabindex="-1" class="jsoneditor-manual-other"></div>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="submit" class="btn btn-success"><{lang key='common_submit'}></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<{include file='common/footer.tpl'}>
<script src="static/editor.md/xml2json.js"></script>
<script src="static/editor.md/jsoneditor-xmltree.js"></script>
<script src="static/editor.md/xmltools.js<{$clear_cache}>"></script>
<script>
$(function(){
    const wizEditor = editormd("redactor_content", {
        height  : 480,
        watch   : false,
        path    : "static/editor.md/lib/",
        toolbarIcons : function() {
            return ["watch", "bold", "italic", "del", "|", "h1","h2","h3","h4","h5","h6", "|", "hr", "code", "|", "list-ul", "list-ol", "|", "link", "image", "fullscreen"]
        }, 
        imageUpload: true,
        imageUploadURL: '?ct=upload&ac=upload',
    });
})
</script>
</body>
</html>

