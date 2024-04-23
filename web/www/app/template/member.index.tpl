<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>

<body>

<div id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="widget-box">

                <div class="widget-content p-b-none">
                    <div class="m-b">
                        <!-- <a href="?ct=member&ac=index" class="btn btn-success"><i class="fa fa-bars"></i>会员列表</a> -->
                        <a href="?ct=member&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>会员添加</a>
                        <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>回收站</a>-->
                    </div>
                </div>

                <div class="widget-content p-b-none">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                       <div class="form-group">
                            <select name="is_pro" class="form-control">         
                                <{html_options options=$is_pro_options selected=$is_pro}>       
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="input-group" id="laydate-range">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                </span>
                                <input type="text" class="form-control" id="laydate-start" name="date_sta" placeholder="Start date" value="<{request_em key='date_sta' default=$date_sta}>" />
                                <span class="input-group-addon input-datepick-divid"> to </span>
                                <input type="text" class="form-control" id="laydate-end" name="date_end" placeholder="End date" value="<{request_em key='date_end' default=$date_end}>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="<{lang key='content_category_search_txt'}>" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white"><{lang key='common_search'}></button>
                        </div>
                    </form>
                </div>

                <div class="widget-content">
                    <form action="" method="POST">
                        <{form_token}>
                        <table class="table table-bordered table-hover table-agl-c with-check">
                            <thead>
                                <tr>
                                    <!-- <th> <input type="checkbox" class="parent" /> </th> -->
                                    <!-- <th> ID </th> -->
                                    <th> Avatar </th>
                                    <th> Username </th>
                                    <th> Nickname </th>
                                    <th> Country </th>
                                    <th> IsPro </th>
                                    <th> Manage </th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$list item=v}>
                                <tr class="layer-photos">
                                    <!-- <td> <input type="checkbox" name="ids[]" value="<{$v.id}>" class="child" /> </td> -->
                                    <!-- <td> <{$v.id}> </td> -->
                                    <td> <{if !empty($v.avatar)}><img src="<{$v.avatar}>" layer-pid="<{$v.id}>" layer-src="<{$v.avatar}>" style="width: 36px;cursor: pointer;" /><{/if}> </td>
                                    <td> <{$v.username}> </td>
                                    <td> <{$v.nickname}> </td>
                                    <td> <{$v.country_cn}> </td>
                                    <td> <{$v.ispro_text}> </td>
                                    <td> 
                                        <a href="?ct=member&ac=edit&id=<{$v.id}>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i>修改</a>
                                            <{if $v.is_pro eq 0}>
                                        <a onclick="plt.confirmAction(event)" data-href="?ct=member&ac=update_is_pro&ids[]=<{$v.id}>" data-title="确认修改？" data-tipmsg="确认修改？" class="btn btn-success btn-xs"><i class="fa fa-edit"></i>Set Manual Pro </a>
                                            <{/if}>
                                        <a onclick="plt.confirmAction(event)" data-href="?ct=member&ac=del&ids[]=<{$v.id}>" data-title="确认删除" data-tipmsg="确认删除" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>删除</a>

                                        <!-- 下拉按钮独立块可以复制粘贴 -->
                                        <div class="btn-group">
                                            <a href="javascript:;" class="btn btn-primary btn-xs" data-toggle="dropdown" aria-expanded="false">更多 <span class="caret"></span></a>
                                            <div role="menu" class="dropdown-menu dropdown-menu-right dropdown-button">
                                                <ul class="list">
                                                    <li class="item">
                                                        <a class="btn btn-primary btn-xs btn-rounded">按钮 1</a>
                                                    </li>
                                                    <li class="item">
                                                        <a class="btn btn-danger btn-xs btn-rounded">按钮 2</a>
                                                    </li>
                                                    <li class="item">
                                                        <a class="btn btn-success btn-xs btn-rounded">按钮 3</a>
                                                    </li>
                                                    <li class="item">
                                                        <a class="btn btn-warning btn-xs btn-rounded">按钮 4</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

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
</div>
<{include file='common/footer.tpl'}>
<script src="static/js/common/datepicker.js"></script>
</body>
</html>
