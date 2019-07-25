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

                <!--<div class="widget-content p-b-none">-->
                    <!--<div class="btn-outline-wrap">-->
                        <!--<a href="?ct=crond&ac=index" class="btn btn-success"><i class="fa fa-bars"></i>任务列表</a>-->
                        <!--<a href="?ct=crond&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>任务添加</a>-->
                    <!--</div>-->
                <!--</div>-->

                <div class="widget-content">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group ">
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="请输入任务名称" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white">搜索</button>
                        </div>
                        <h6 class="help-block" style="margin-top:0;margin-bottom:0;">
                            <i class="fa fa-info-circle"></i> 
                            服务器时间 <font id="time" color="green"><{$ns|date_format:"%Y-%m-%d %H:%M:%S"}></font> (用于参考设置任务执行时间)
                        </h6>
                    </form>
                </div>
                <div class="widget-content">
                    <form action="" method="POST">
                        <{form_token}>
                        <table class="table table-bordered table-hover table-agl-c">
                            <thead>
                                <tr>
                                    <th>排序</th>
                                    <th>任务名称</th>
                                    <th>任务脚本</th>
                                    <th>上次执行时间</th>
                                    <th>上次运行时间</th>
                                    <th>时间格式</th>
                                    <th>状态</th>
                                    <th>管理</th>
                                </tr>
                            </thead>
                            <tbody>
                                <{if !empty($list)}>
                                <{foreach from=$list item=v}>
                                <tr>
                                    <td> <input type="text" class="form-control" name="sorts[<{$v.id}>]" value="<{$v.sort}>" style="width:50px;" /> </td>
                                    <td> <{$v.name}> </td>
                                    <td> <{$v.filename}> </td>
                                    <td> <{if !$v.lasttime}><font color="red">尚未执行</font><{else}><{$v.lasttime|date_format:"%m-%d %H:%M:%S"}><{/if}> </td>
                                    <td> <{$v.runtime}> </td>
                                    <td> <{$v.runtime_format}> </td>
                                    <td> <{if $v.status}><font color="green">启动</font><{else}><font color="red">停止</font><{/if}> </td>
                                    <td>
                                        <{if $v.status}>
                                        <a href="?ct=crond&ac=status&ids[]=<{$v.id}>&status=0" class="btn btn-danger btn-xs"><i class="fa fa-arrow-down"></i>停止</a>
                                        <{else}>
                                        <a href="?ct=crond&ac=status&ids[]=<{$v.id}>&status=1" class="btn btn-success btn-xs"><i class="fa fa-arrow-up"></i>启动</a>
                                        <{/if}>
                                        <a href="?ct=crond&ac=edit&id=<{$v.id}>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i>修改</a>
                                        <a onclick="plt.confirmAction(event)" data-href="?ct=crond&ac=del&ids[]=<{$v.id}>" data-title="确认删除" data-tipmsg="是否确认删除"  class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>删除</a>
                                    </td>
                                </tr>
                                <{/foreach}>
                                <{/if}>
                                <tr>
                                    <td colspan="8">
                                        <div class="fl">
                                            <a data-href="?ct=crond&amp;ac=batch_edit" class="btn btn-success" onclick="plt.subform(event)" data-title="确定排序" data-tipmsg="是否确定排序" data-errmsg="操作有误"><i class="fa fa-edit"></i>排序</a>
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
<script type="text/javascript">
var ns = <{$ns}>*1000;//数字要*1000，因为js是毫秒数！
disptime(ns);
function disptime(ns)
{
    time = formatDate(ns);
    $('#time').html(time);
    ns_next = parseInt(ns)+1000;//数字要*1000，因为js是毫秒数！
    var Mytime=setTimeout("disptime(ns_next)", 1000);
}
function formatDate(ns)
{
    var time = new Date(ns);
    var year = time.getFullYear();
    var month = time.getMonth()+1;
    var date = time.getDate();
    var hour = time.getHours();
    var minute = time.getMinutes();
    var second = time.getSeconds();
    /*判断是不是要前置补零，一般情况下补零，不解释*/
    if(month<10)
        month = "0"+month;
    if(date<10)
        date = "0"+date;
    if(hour<10)
        hour = "0"+hour;
    if(minute<10)
        minute = "0"+minute;
    if(second<10)
        second = "0"+second;
    return year+"-"+month+"-"+date+" "+hour+":"+minute+":"+second;
}
</script>

</body>
</html>
