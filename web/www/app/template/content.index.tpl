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

                <div class="widget-content p-b-none">
                    <div class="btn-outline-wrap">
                        <a href="?ct=content&ac=index" class="btn btn-success"><i class="fa fa-bars"></i>内容列表</a>
                        <a href="?ct=content&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>内容添加</a>
                        <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>垃圾桶</a>-->
                    </div>
                </div>

                <div class="widget-content p-b-none">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group">
                            <!--<label>分类</label>-->
                            <select name="catid" class="form-control">         
                                <{html_options options=$options selected=$catid}>       
                            </select>
                        </div>
                        <div class="form-group">
                            <!--<label>关键字</label>-->
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="请输入标题" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white">搜索</button>
                        </div>
                    </form>
                </div>
                <div class="widget-content">
                    <table class="table table-bordered table-hover table-agl-c with-check">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>标题</th>
                                <th>图片</th>
                                <th>添加时间</th>
                                <th>修改时间</th>
                                <th>管理</th>
                            </tr>
                        </thead>
                        <tbody>
                            <{foreach from=$list item=v}>
                            <tr>
                                <td> <{$v.id}> </td>
                                <td> <{$v.name}> </td>
                                <td> <img src="<{$v.imageurl}>" width="100px" height="100px" /> </td>
                                <td> <{$v.create_time|date_format:'%Y-%m-%d %H:%M:%S'}> </td>
                                <td> <{$v.update_time|date_format:'%Y-%m-%d %H:%M:%S'}> </td>
                                <td> 
                                    <a href="?ct=content&ac=info&id=<{$v.id}>" class="btn btn-success btn-xs"><i class="fa fa-search"></i>查看</a>
                                    <a href="?ct=content&ac=edit&id=<{$v.id}>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i>修改</a>
                                    <a onclick="plt.confirmAction(event)" data-href="?ct=content&ac=del&ids[]=<{$v.id}>" data-title="确认删除" data-tipmsg="确认删除" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>删除</a>
                                </td>
                            </tr>
                            <{foreachelse}>
                            <tr>
                                <td colspan="6">暂无内容</td>
                            </tr>
                            <{/foreach}>
                            <tr>
                                <td colspan="6">
                                    <div class="fl">
                                        <!--<a class="btn btn-outline btn-danger active"><i class="fa fa-trash-o"></i>批量删除</a>-->
                                        <a class="btn btn-outline btn-danger active ajax-post-test"><i class="fa fa-trash-o"></i>批量删除</a>
                                    </div>
                                    <div class="fr">
                                        <{$pages}>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/main.js"></script>
<script>
$('.ajax-post-test').click(function(){

    $.post("?ct=content&ac=ajax_post",{suggest:'jjjjj'},function(result){
        console.log(result);
    });

});
</script>

</body>
</html>

