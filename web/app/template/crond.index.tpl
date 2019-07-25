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

                <div class="widget-content">
                    <form class="form-inline" action="" method="GET">
                        <h6 class="help-block" style="margin-top:0;margin-bottom:0;">
                            <i class="fa fa-info-circle"></i> 
                            服务器时间 <font id="time" color="green"><{$ns|date_format:"%Y-%m-%d %H:%M:%S"}></font> (用于参考设置任务执行时间)
                        </h6>
                    </form>
                </div>
                <div class="widget-content">
                    <table class="table table-bordered table-hover table-agl-c">
                        <thead>
                            <tr>
                                <th>时间格式</th>
                                <th>任务脚本</th>
                                <th>上次执行时间</th>
                                <th>上次运行时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <{foreach from=$list item=v}>
                            <tr>
                                <td> <{$v.runtime_format}> </td>
                                <td> <{$v.filename}> </td>
                                <td> <{if !$v.lasttime}><font color="red">尚未执行</font><{else}><{$v.lasttime|date_format:"%m-%d %H:%M:%S"}><{/if}> </td>
                                <td> <{if !$v.runtime}><font color="red">尚未执行</font><{else}><{$v.runtime}><{/if}> </td>
                            </tr>
                            <{foreachelse}>
                            <tr>
                                <td colspan="4">暂无任务</td>
                            </tr>
                            <{/foreach}>
                        </tbody>
                    </table>
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
