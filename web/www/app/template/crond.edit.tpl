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
            <!--<div class="btn-outline-wrap">-->
                <!--<a href="javascript:history.back(-1)" class="btn btn-success btn-outline"><i class="fa fa-chevron-left"></i>返回</a>-->
                <!--<a href="?ct=crond&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>计划任务列表</a>-->
                <!--<a href="?ct=crond&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>计划任务添加</a>-->
                <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i>垃圾桶</a>-->
            <!--</div>-->

            <div class="widget-box">
                <form class="form-horizontal" id="validateForm" novalidate="novalidate" action="" method="POST">

                    <{form_token}>

                    <div class="widget-title">
                        <span class="icon"><a href="javascript:history.back(-1)"><i class="fa fa-chevron-left"></i>返回</a></span>
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <!--<h5>基本信息</h5>-->
                    </div>
                    <div class="widget-content">

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 任务名称 :</label>
                            <div class="col-sm-10">
                                <input name="name" type="text" class="form-control" value="<{$v.name}>" datatype="*"  nullmsg="请输入任务名称" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">任务脚本 :</label>
                            <div class="col-sm-10">
                                <textarea name="filename" rows="5" cols="10" class="form-control"><{$v.filename}></textarea>
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 不能包含路径，程序脚本必须存放于 ./core/crond/ 目录中，一行一个脚本6位</span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">服务器时间 :</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"> <font id="time" color="green"><{$ns|date_format:"%Y-%m-%d %H:%M:%S"}></font> (用于参考设置任务执行时间)</p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 任务执行时间 :</label>
                            <div class="col-sm-10">
                                <input name="runtime_format" type="text" class="form-control" value="<{$v.runtime_format}>" datatype="*"  nullmsg="请输入任务执行时间" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">任务执行时间 :</label>
                            <div class="col-sm-10">
<pre>
'*',        //每分钟
'*:i',      //每小时 某分
'H:i',      //每天 某时:某分
'@-w H:i',  //每周-某天 某时:某分  0=周日
'*-d H:i',  //每月-某天 某时:某分
'm-d H:i',  //某月-某日 某时-某分
'Y-m-d H:i',//某年-某月-某日 某时-某分
</pre>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">状态 :</label>
                            <div class="col-sm-10">
                                <div class="radio">
                                <label><input type="radio" name="status" value="1" <{if $v.status == 1}>checked<{/if}> /> 启动 </label>
                                <label><input type="radio" name="status" value="0" <{if $v.status == 0}>checked<{/if}> /> 停止 </label>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button type="submit" class="btn btn-success">提交</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="static/frame/js/main.js"></script>
<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/validform.js"></script>
<script src="static/frame/js/newvalidform.js"></script>
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
