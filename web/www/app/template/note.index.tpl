<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>

<body>

<div id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="btn-outline-wrap">
                <a href="?ct=note&ac=index" class="btn btn-success"><i class="fa fa-bars"></i>笔记列表</a>
                <a href="?ct=note&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>笔记添加</a>
                <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>垃圾桶</a>-->
            </div>

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

            <table class="table table-bordered table-hover table-agl-c with-check">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>标题</th>
                        <th>添加时间</th>
                        <th>管理</th>
                    </tr>
                </thead>
                <tbody>
                    <{foreach from=$list item=v}>
                    <tr>
                        <td> <{$v.id}> </td>
                        <td> <{$v.name}> </td>
                        <td> <{$v.create_time|date_format:'%Y-%m-%d %H:%M:%S'}> </td>
                        <td> 
                            <a href="?ct=note&ac=info&id=<{$v.id}>" class="btn btn-success btn-xs" target="_blank"><i class="fa fa-search"></i>查看</a>
                            <a href="?ct=note&ac=edit&id=<{$v.id}>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i>修改</a>
                            <a onclick="plt.confirmAction(event)" data-href="?ct=note&ac=del&ids[]=<{$v.id}>" data-title="确认删除" data-tipmsg="确认删除" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>删除</a>
                        </td>
                    </tr>
                    <{foreachelse}>
                    <tr>
                        <td colspan="6">暂无笔记</td>
                    </tr>
                    <{/foreach}>
                    <tr>
                        <td colspan="6">
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
<{include file='common/footer.tpl'}>
</body>
</html>

