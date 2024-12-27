<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>
<body>
<body>

<div id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="widget-box">

                <div class="widget-content p-b-none">
                    <div class="btn-outline-wrap">
                        <a href="?ct=app_version&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>版本添加</a>
                    </div>
                </div>

                <div class="widget-content p-b-none">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group">
                            <select name="os" class="form-control">         
                                <{html_options options=$os_options selected=$os}>       
                            </select>
                        </div>
                        <div class="form-group">
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="<{lang key='search_tips'}>" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white"><{lang key='common_search'}></button>
                        </div>
                    </form>
                </div>
                <div class="widget-content">
                    <table class="table table-bordered table-hover table-agl-c with-check">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>版本</th>
                                <th>系统</th>
                                <th>包名</th>
                                <th width="180px">添加时间</th>
                                <th width="180px">管理</th>
                            </tr>
                        </thead>
                        <tbody>
                            <{foreach from=$list item=v}>
                            <tr class="layer-photos">
                                <td> <{$v.id}> </td>
                                <td> <{$v.version}> </td>
                                <td> <{$v.os}> </td>
                                <td> <{$v.bound_id}> </td>
                                <td> <{$v.addtime|date_format:'%Y-%m-%d %H:%M:%S'}> </td>
                                <td> 
                                    <a href="?ct=app_version&ac=edit&id=<{$v.id}>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i>Modify</a>
                                    <a onclick="plt.confirmAction(event)" data-href="?ct=app_version&ac=del&ids[]=<{$v.id}>" data-title="确认删除" data-tipmsg="确认删除" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>Delete</a>
                                </td>
                            </tr>
                            <{foreachelse}>
                            <tr>
                                <td colspan="10"><{lang key='nullrecord_tips'}></td>
                            </tr>
                            <{/foreach}>
                            <tr>
                                <td colspan="10">
                                    <div class="fl">
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
<{include file='common/footer.tpl'}>
</body>
</html>

