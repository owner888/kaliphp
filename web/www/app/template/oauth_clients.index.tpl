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
            <div class="btn-outline-wrap">
                <a href="?ct=oauth_clients&ac=index" class="btn btn-success"><i class="fa fa-bars"></i>应用列表</a>
                <a href="?ct=oauth_clients&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>应用添加</a>
                <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>垃圾桶</a>-->
            </div>

            <form class="form-inline" action="" method="GET">
                <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                <div class="form-group">
                    <!--<label>关键字</label>-->
                    <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="应用名称" />
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-white"><{lang key='common_search'}></button>
                </div>
            </form>

            <form action="" method="POST">
                <{form_token}>
            <table class="table table-bordered table-hover table-agl-c with-check">
                <thead>
                    <tr>
                        <th> <input type="checkbox" class="parent" /> </th>
                        <th> ID </th>
                        <th> 应用名称 </th>
                        <th> 应用简介 </th>
                        <th> 安全域名 </th>
                        <th> 安全IP </th>
                        <th> 管理 </th>
                    </tr>
                </thead>
                <tbody>
                    <{foreach from=$list item=v}>
                    <tr>
                        <td> <input type="checkbox" name="ids[]" value="<{$v.id}>" class="child" /> </td>
                        <td> <{$v.id}> </td>
                        <td> <{$v.name}> </td>
                        <td> <{$v.desc}> </td>
                        <td> <{$v.domain}> </td>
                        <td> <{$v.ip}> </td>
                        <td> 
                            <a href="?ct=oauth_clients&ac=edit&id=<{$v.id}>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i><{lang key='common_edit'}></a>
                            <a onclick="plt.confirmAction(event)" data-href="?ct=oauth_clients&ac=del&ids[]=<{$v.id}>" data-title="<{lang key='common_sure_delete'}>" data-tipmsg="确认删除" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i><{lang key='common_delete'}></a>
                        </td>
                    </tr>
                    <{foreachelse}>
                    <tr>
                        <td colspan="7">暂无应用</td>
                    </tr>
                    <{/foreach}>
                    <tr>
                        <td colspan="7">
                            <div class="fl">
                                <a data-href="?ct=oauth_clients&ac=del" class="btn btn-danger" onclick="plt.subform(event,'child')"
                                   data-title="<{lang key='common_sure_batch_delete'}>"
                                   data-tipmsg="确认批量删除" data-errmsg="请先选择"
                                ><i class="fa fa-trash-o"></i><{lang key='common_batch_delete'}></a>
                            </div>
                            <div class="fr">
                                <{$pages}>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
    </div>

</div>

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/main.js<{$clear_cache}>"></script>

</body>
</html>

