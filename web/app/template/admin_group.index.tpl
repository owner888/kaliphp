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
                        <a href="?ct=admin_group&ac=index" class="btn btn-success"><i class="fa fa-bars"></i>用户组列表</a>
                        <a href="?ct=admin_group&ac=add" class="btn btn-outline btn-info"><i class="fa fa-plus-circle"></i>用户组添加</a>
                        <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>垃圾桶</a>-->
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
                    <table class="table table-bordered table-hover table-agl-c">
                        <thead>
                            <tr>
                                <th align="left">用户组名</th>
                                <th width="150px">管理</th>
                            </tr>
                        </thead>
                        <tbody>
                            <{if !empty($list)}>
                            <{foreach from=$list item=v}>
                            <tr>
                                <td align="left"> <{$v.name}> </td>
                                <td>
                                    <{if $v.id != 1}>
                                    <a href="?ct=admin_group&ac=edit&id=<{$v.id}>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i>修改</a>
                                    <a onclick="plt.confirmAction(event)" data-href="?ct=admin_group&ac=del&id=<{$v.id}>"
                                        data-title="确认删除"
                                        data-tipmsg="确认进行该操作吗？"
                                    class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>删除</a>
                                    <{else}>
                                    <button href="javascript:;" class="btn btn-xs btn-dark" disabled="disabled">不可操作</button>
                                    <{/if}>
                                </td>
                            </tr>
                            <{/foreach}>
                            <{/if}>
                            <tr>
                                <td colspan="3">
                                    <div class="fl">
                                        <!--<a class="btn btn-outline btn-danger active"><i class="fa fa-trash-o"></i>批量删除</a>-->
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
</body>
</html>

