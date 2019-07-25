<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><{$title}></title>
    <link href="static/frame/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="static/frame/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="static/frame/css/animate.min.css" rel="stylesheet">
    <link href="static/frame/css/main.css" rel="stylesheet">
    <script src="static/frame/js/jquery.min.js?v=2.1.4"></script>
    <style type="text/css">
    /* 复制提示 */
    .copy-tips{position:fixed;z-index:999;bottom:50%;left:50%;margin:0 0 -20px -80px;background-color:rgba(0, 0, 0, 0.2);filter:progid:DXImageTransform.Microsoft.Gradient(startColorstr=#30000000, endColorstr=#30000000);padding:6px;}
    .copy-tips-wrap{padding:10px 20px;text-align:center;border:1px solid #F4D9A6;background-color:#FFFDEE;font-size:14px;}
    </style>
</head>

<body>

<div id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="widget-box">

                <div class="widget-content p-b-none">
                    <div class="btn-outline-wrap">
                        <a href="?ct=filemanage&ac=index" class="btn btn-success"><i class="fa fa-bars"></i>文件列表</a>
                        <a href="?ct=filemanage&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>文件添加</a>
                    </div>
                </div>

                <div class="widget-content p-b-none">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group">
                            <!--<label>关键字</label>-->
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="请输入名称" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white">搜索</button>
                        </div>
                    </form>
                </div>

                <div class="widget-content">
                    <form action="" method="POST">
                        <{form_token}>
                        <table class="table table-bordered table-hover table-agl-c with-check">
                            <thead>
                                <tr>
                                    <th> <input type="checkbox" class="parent" /> </th>
                                    <th> 文件名称 </th>
                                    <th> 文件大小 </th>
                                    <th> 修改时间 </th>
                                    <th> 刷新CDN </th>
                                    <th> 复制地址 </th>
                                    <th> 管理 </th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$list item=v}>
                                <tr>
                                    <td> <input type="checkbox" name="names[]" value="<{$v.name}>" class="child" /> </td>
                                    <td> <{$v.name}> </td>
                                    <td> <{$v.filesize}> </td>
                                    <td> 
                                        <{if $v.filemtime|date_format:'%Y-%m-%d' == $smarty.now|date_format:'%Y-%m-%d'}>
                                        <font color="red"><{$v.filemtime}></font>
                                        <{else}>
                                        <{$v.filemtime}> 
                                        <{/if}>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary btn-xs" href="?ct=filemanage&ac=refresh_cdn&bcdn_url=<{$v.bcdn_url|urlencode}>">刷新CDN</a>
                                    </td>
                                    <td style="position:relative;">
                                        <input type="hidden" class="input" value="<{$v.bcdn_url}>" />
                                        <a class="copy-input btn btn-primary btn-xs">复制地址</a>
                                    </td>
                                    <td>
                                        <a href="<{$v.url}>" class="btn btn-success btn-xs" target="_blank">本地下载</a>
                                        <a href="<{$v.bcdn_url}>" class="btn btn-success btn-xs" target="_blank">CDN下载</a>
                                        <a onclick="plt.confirmAction(event)" data-href="?ct=filemanage&ac=del&names[]=<{$v.name}>" data-title="确认删除" data-tipmsg="是否确定删除"  class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>删除</a>
                                    </td>
                                </tr>
                                <{foreachelse}>
                                <tr>
                                    <td colspan="7">暂无文件</td>
                                </tr>
                                <{/foreach}>
                                <tr>
                                    <td colspan="7">
                                        <div class="fl">
                                            <a data-href="?ct=filemanage&ac=del" class="btn btn-danger" onclick="plt.subform(event,'child')" data-title="确定批量删除" data-tipmsg="是否确定批量删除" data-errmsg="请先选择"><i class="fa fa-trash-o"></i>批量删除</a>
                                        </div>
                                        <div class="fr">
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="url" style="display:none"><div>
<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/main.js<{$clear_cache}>"></script>
<script src="static/frame/js/plugins/layer/layer.min.js"></script>
<script type="text/javascript">
    $(function(){
        $("body").on("click",".copy-input",function(){
            var _this = $(this);
            $("#url").html(_this.siblings(".input").val());
            var Url2=document.getElementById("url").innerText;
            $(".oInput").remove();
            var oInput = document.createElement('input');
            oInput.value = Url2;
            document.body.appendChild(oInput);
            oInput.select(); // 选择对象
            document.execCommand("Copy"); // 执行浏览器复制命令
            oInput.className = 'oInput';
            oInput.style.display='none';
            layer.msg('☺ 复制成功');
        })
    });
</script>
</body>
</html>

