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
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="<{lang key='search_tips'}>" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white"><{lang key='search'}></button>
                        </div>
                    </form>
                </div>
                <div class="widget-content">
                    <form action="" method="POST">
                        <{form_token}>
                        <table class="table table-bordered table-hover table-agl-c">
                            <thead>
                                <tr>
                                    <!--<th> SESSION ID </th>-->
                                    <th> 用户 </th>
                                    <th> 登录地址 </th>
                                    <th> 登录国家 </th>
                                    <th> 执行动作 </th>
                                    <th> 登陆时间 </th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$list item=v}>
                                <tr>
                                    <!--<td> <{$v.session_id}> </td>-->
                                    <td> <{$v.username}> </td>
                                    <td> <{$v.loginip}> </td>
                                    <td> <{$v.logincountry}> </td>
                                    <td> <{$v.actions}> </td>
                                    <td> <{$v.logintime|date_format:'%Y-%m-%d %H:%M:%S'}> </td>
                                </tr>
                                <{foreachelse}>
                                <tr>
                                    <td colspan="9">暂无分类</td>
                                </tr>
                                <{/foreach}>
                                <tr>
                                    <td colspan="9">
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
</body>
</html>
