<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
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
            <div class="m-b">
                <a href="?ct=user&ac=index" class="btn btn-success"><i class="fa fa-bars"></i>会员列表</a>
                <a href="?ct=user&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>会员添加</a>
                <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>回收站</a>-->
            </div>

            <div class="widget-box">
                <div class="widget-content nopadding">
                    <form class="form-inline m-t m-l" action="" method="GET">
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

                <form action="" method="POST">
                    <{form_token}>
                    <table class="table table-bordered table-hover table-agl-c with-check">
                        <thead>
                            <tr>
                                <th> <input type="checkbox" class="parent" /> </th>
                                <th> 会员名称 </th>
                                <th> 年龄 </th>
                                <th> 邮箱 </th>
                                <th> 添加时间 </th>
                                <th> 管理 </th>
                            </tr>
                        </thead>
                        <tbody>
                            <{foreach from=$list item=v}>
                            <tr>
                                <td> <input type="checkbox" name="ids[]" value="<{$v.id}>" class="child" /> </td>
                                <td> <{$v.username}> </td>
                                <td> <{$v.age}> </td>
                                <td> <{$v.email}> </td>
                                <td> <{$v.create_time|date_format:'%Y-%m-%d %H:%M:%S'}> </td>
                                <td> 
                                    <a href="?ct=user&ac=edit&id=<{$v.id}>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i>修改</a>
                                    <a onclick="plt.confirmAction(event)" data-href="?ct=user&ac=del&ids[]=<{$v.id}>" data-title="确认删除" data-tipmsg="确认删除" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>删除</a>
                                </td>
                            </tr>
                            <{foreachelse}>
                            <tr>
                                <td colspan="8">暂无会员</td>
                            </tr>
                            <{/foreach}>
                            <tr>
                                <td colspan="8">
                                    <div class="fl">
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
</div>
<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/main.js<{$clear_cache}>"></script>
</body>
</html>
