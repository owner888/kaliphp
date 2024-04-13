<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title><{$app_name}></title>
    <link href="static/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="static/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="static/css/animate.min.css" rel="stylesheet">
    <link href="static/css/plugins/datapicker/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">
    <script src="static/frame/js/jquery.min.js?v=2.1.4"></script>
</head>

<body>

<div id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="widget-box">

                <div class="widget-content p-b-none">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group">
                            <div class="input-daterange"  data-language="zh-CN" style="overflow: hidden">
                                <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                    </span>
                                    <input type="text" class="form-control" data-plugin="start" name="date_sta" value="<{request_em key='date_sta' default=$date_sta}>" />
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"> to </span>
                                    <input type="text" class="form-control" data-plugin="end" name="date_end" value="<{request_em key='date_end' default=$date_end}>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <select name="uid" class="form-control" onchange="location.href='?ct=<{request_em key='ct'}>&ac=<{request_em key='ac'}>&uid='+this.value">         
                                <{html_options options=$user_options selected=$uid}>       
                            </select>
                        </div>
                        <div class="form-group">
                            <!--<label>关键字</label>-->
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="请输入关键字搜索" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white">搜索</button>
                        </div>
                    </form>
                </div>

                <div class="widget-content ">
                    <form action="" method="POST">
                        <{form_token}>
                        <table class="table table-bordered table-hover table-agl-c with-check">
                            <thead>
                                <tr>
                                    <th> <input type="checkbox" class="parent" /> </th>
                                    <th> 用户 </th>
                                    <th> 登录地址 </th>
                                    <th> 登录国家 </th>
                                    <th> 执行动作 </th>
                                    <th> 登陆时间 </th>
                                    <th> 无操作时长 </th>
                                    <th> 操作 </th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$list item=v}>
                                <tr>
                                    <td> <input type="checkbox" name="uids[]" value="<{$v.uid}>" class="child" /> </td>
                                    <td> <{$v.username}> </td>
                                    <td> <{$v.loginip}> </td>
                                    <td> <{$v.logincountry}> </td>
                                    <td> <{$v.actions}> </td>
                                    <td> <{$v.logintime|date_format:'%Y-%m-%d %H:%M:%S'}> </td>
                                    <td> <{$v.expires}> </td>
                                    <td> 
                                        <a onclick="plt.pormpt(event)" data-href="?ct=session&ac=terminate&uids[]=<{$v.uid}>" data-title="是否确定终断"  class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>终断</a>
                                    </td>
                                </tr>
                                <{foreachelse}>
                                <tr>
                                    <td colspan="9">暂无分类</td>
                                </tr>
                                <{/foreach}>
                                <tr>
                                    <td colspan="9">
                                        <div class="fl">
                                            <a data-href="?ct=session&ac=terminate" class="btn btn-danger" onclick="plt.pormptBatch(event,'child')" data-title="批量终断" data-tipmsg="确认批量终断" data-errmsg="请先选择" ><i class="fa fa-trash-o"></i>批量终断</a>
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
<script src="static/frame/js/plugins/datapicker/bootstrap-datetimepicker.min.js"></script>
<script src="static/frame/js/plugins/datapicker/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="static/frame/js/main.js<{$clear_cache}>"></script>
</body>
</html>
