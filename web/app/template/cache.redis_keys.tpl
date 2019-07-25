<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" category="width=device-width, initial-scale=1.0">
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

                <!--<div class="widget-content p-b-none">-->
                    <!--<div class="btn-outline-wrap">-->
                        <!--<a href="?ct=cache&ac=redis_keys" class="btn btn-success"><i class="fa fa-bars"></i>Redis列表</a>-->
                        <!--<a href="?ct=cache&ac=redis_add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>Redis添加</a>-->
                    <!--</div>-->
                <!--</div>-->

                <div class="widget-content">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group">
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="请输入Key关键字" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white">搜索</button>
                        </div>
                        <h6 class="help-block" style="margin-top:0;margin-bottom:0;">
                            <i class="fa fa-info-circle"></i> 
                            注意要按照redis的key规则，例如：<font color="red">keyword*</font> 或 <font color="red">*keyword*</font>
                        </h6>
                    </form>
                </div>

                <div class="widget-content">
                    <form action="" method="POST">
                        <{form_token}>
                        <table class="table table-bordered table-hover table-agl-c with-check">
                            <thead>
                                <tr>
                                    <th> <input type="checkbox" class="parent" /> </th>
                                    <th> ID </th>
                                    <th> Redis Key </th>
                                    <th> 类型 </th>
                                    <th> 队列长度 </th>
                                    <th> 剩余超时时间 </th>
                                    <th> 管理 </th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$list key=k item=v}>
                                <tr>
                                    <td> <input type="checkbox" name="keys[]" value="<{$v.key}>" class="child" /> </td>
                                    <td> <{$k+1}> </td>
                                    <td> <{$v.key}> </td>
                                    <td> <{$v.type}> </td>
                                    <td> <{if $v.len}><{$v.len}><{else}>-<{/if}> </td>
                                    <td> <{if $v.ttl > 0}><{$v.ttl}><{elseif $v.ttl == -1}>永不超时<{else}>不存在<{/if}> </td>
                                    <td> 
                                        <a href="?ct=cache&ac=show_cache&key=<{$v.key}>" class="btn btn-success btn-xs" target="_blank"><i class="fa fa-search"></i>查看</a>
                                        <a onclick="plt.confirmAction(event)" data-href="?ct=cache&ac=del&keys[]=<{$v.key}>" data-title="确认删除" data-tipmsg="是否确定删除" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>删除</a>
                                    </td>
                                </tr>
                                <{foreachelse}>
                                <tr>
                                    <td colspan="7">暂无Redis</td>
                                </tr>
                                <{/foreach}>
                                <tr>
                                    <td colspan="7">
                                        <div class="fl">
                                            <a data-href="?ct=cache&ac=del" class="btn btn-danger" onclick="plt.subform(event,'child')" data-title="确定批量删除" data-tipmsg="是否确定批量删除" data-errmsg="请先选择"><i class="fa fa-trash-o"></i>批量删除</a>
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

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/main.js"></script>
</body>
</html>

