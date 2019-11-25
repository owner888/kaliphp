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
                        <a href="?ct=config&ac=index" class="btn btn-success"><i class="fa fa-bars"></i>配置列表</a>
                        <a href="?ct=config&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>配置添加</a>
                    </div>
                </div>

                <div class="widget-content">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group">
                            <select name="group" class="form-control">         
                                <{html_options options=$options selected=$group}>       
                            </select>
                        </div>
                        <div class="form-group">
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="请输入配置名" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white">搜索</button>
                        </div>
                        <h6 class="help-block" style="margin-top:0;margin-bottom:0;">
                            <i class="fa fa-info-circle"></i> 
                            系统配置变量，在程序中用 <font color="red">config::instance('db_config')->get(varname, group)</font> 调用
                        </h6>
                    </form>
                </div>

                <div class="widget-content">
                    <form action="" method="POST">
                        <{form_token}>
                        <table class="table table-bordered table-hover table-agl-c">
                            <thead>
                                <tr>
                                    <th>变量组</th>
                                    <th width="50px">排序</th>
                                    <th>变量说明</th>
                                    <th>变量名</th>
                                    <th>变量值</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{if !empty($list)}>
                                <{foreach from=$list item=v}>
                                <tr>
                                    <td> <{$v.group}> </td>
                                    <td> 
                                        <input type='text' name='sorts[<{$v.name}>]' value='<{$v.sort}>' class='form-control' style="width:50px;" />
                                    </td>
                                    <td> <a href="?ct=config&ac=edit&name=<{$v.name}>"><i class="fa fa-edit"></i> <{$v.title}></a> </td>
                                    <td> <{$v.name}> </td>
                                    <td> 
                                        <{if $v.type=='bool'}>
                                        <label><input type="radio" name="datas[<{$v.name}>]" value="1" <{if $v.value==1}>checked<{/if}> /> 是</label> &nbsp;
                                        <label><input type="radio" name="datas[<{$v.name}>]" value="0" <{if $v.value==0}>checked<{/if}> /> 否</label>
                                        <{elseif $v.type=='text'}>
                                        <textarea name='datas[<{$v.name}>]' class='form-control'><{$v.value}></textarea>
                                        <{else}>
                                        <input type="text" name="datas[<{$v.name}>]" value="<{$v.value}>" class="form-control" />
                                        <{/if}>
                                    </td>
                                </tr>
                                <{/foreach}>
                                <{/if}>
                                <tr>
                                    <td colspan="8">
                                        <div class="fl">
                                            <a data-href="?ct=config&amp;ac=batch_edit" class="btn btn-primary" onclick="plt.subform(event)"
                                                data-title="确定修改" data-tipmsg="批量修改" date-errmsg="操作有误"><i class="fa fa-edit"></i>批量修改</a>
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

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/main.js"></script>
</body>
</html>
