<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title><{$app_name}></title>
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
                <!--<a href="?ct=admin&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>用户列表</a>-->
                <!--<a href="?ct=admin&ac=add" class="btn btn-outline btn-info"><i class="fa fa-plus-circle"></i>用户添加</a>-->
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
                            <label class="col-sm-2 control-label">用户名 :</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"> <{$v.username}></p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户密码:</label>
                            <div class="col-sm-10">
                                <a onclick="confirmAction('reset')" class="btn btn-success" href="javascript:;"><i class="fa fa-refresh"></i>重置密码</a>
                                <a onclick="confirmAction('one')" class="btn btn-success" href="javascript:;">生成一次性密码</a>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 真实姓名:</label>
                            <div class="col-sm-10">
                                <input type="text" name='realname' class="form-control" value="<{$v.realname}>" datatype="*" nullmsg="请输入真实姓名" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Email:</label>
                            <div class="col-sm-10">
                                <input type="email" name='email' class="form-control" value="<{$v.email}>" ignore="ignore" datatype="e" errmsg="请输入正确的邮箱"/>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">信任IP:</label>
                            <div class="col-sm-10">
                                <input type="text" name='safe_ips' class="form-control" value="<{$v.safe_ips}>" />
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 多个IP以逗号分隔，一旦添加信任IP，用户就只能在信任IP环境下进行登陆和操作</span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">登录时长:</label>
                            <div class="col-sm-10">
                                <input type="text" name='session_expire' class="form-control" value="<{$v.session_expire}>" />
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 以秒为单位，在此时间内用户无操作会自动退出登录，默认1440秒，即24分钟</span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户组:</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                <{foreach from=$group_options key=kk item=vv}>
                                <{if $kk != '0'}>
                                <label><input type='checkbox' name='groups[]' value='<{$kk}>' <{if in_array($kk, $v.groups) }> checked='checked'<{/if}> /> <{$vv}></label>
                                <{/if}>
                                <{/foreach}>

                                <div class="hr-line-dashed"></div>

                                <a href='?ct=admin&ac=purview&id=<{$v.uid}>&gourl=<{$gourl}>' class="btn btn-primary">设置独立权限</a>
                                <a onclick="plt.confirmAction(event)" data-href="?ct=admin&ac=purview_del&id=<{$v.uid}>&gourl=<{$gourl}>" data-title="独立权限" data-tipmsg="确定清除独立权限?" class="btn btn-danger">清除独立权限</a>
                                </div>
                                <div style="height:10px"></div>
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 用户权限 = 所属组权限 + 独立权限</span>
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


<!--首次登陆-->
<div id="edit-password-pup" style="display: none" class="edit-password-pup">
    <form  method="post" class="form-horizontal" style="padding:0 10px;" id="edit-password-form">
        <div class="form-group">
            <p style="margin-bottom:0">
                <input type="checkbox" name="check1"  class="lcs_check" autocomplete="off" checked="checked"/>
                &nbsp;&nbsp;自动生成密码
            </p>
        </div>
        <div class="form-group enter-form" style="position: relative;margin:0 -16px 16px;display:none">
            <input type="password" class="form-control" id="edit-password" name="pwd" placeholder="请输入密码"  >
            <i class="fa fa-eye eye-btn" style="position: absolute;right:10px;top:10px;" ></i>
            <span class="Validform_checktip Validform_wrong" style="color:#ed5565;display:none;font-size:12px;"><i class="fa fa-times-circle"></i> 必须包含数字，大小写字母的6-18位的密码！</span>
        </div>
       <div class="form-group one-item" style="margin-bottom:0">
           <p style="margin-bottom:0">
               <input type="checkbox" name="check2"  class="lcs_check lcs_tt1"  autocomplete="off" checked="checked"/>
               &nbsp;&nbsp;要求在下次登陆时更改密码
           </p>
       </div>
    </form>
</div>

<!--首次登陆-->
<div id="success-pup" style="display: none" class="success-pup">
    <form  method="post" class="form-horizontal" style="padding:0 10px;" id="success">
        <div class="form-group" style="position: relative;margin:0 0 10px 0">
            <input type="password" class="form-control" id="password">
            <i class="fa fa-eye eye-btn" style="position: absolute;right:10px;top:10px;" ></i>
        </div>
        <p class="copy-btn" style="cursor: pointer;margin:0">点击即可复制密码</p>
    </form>
</div>

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/validform.js"></script>
<script src="static/frame/js/newvalidform.js"></script>
 <script src="static/frame/js/plugins/layer/layer.min.js"></script>
<script src="static/frame/js/main.js"></script>
<script>
    function confirmAction(state){
        var type=0,height="230px";
        if(state=="one"){
            $(".one-item").hide();
            type=1;height="180px";
        }else{
            type=0;height="230px"
            $(".one-item").show();
        }
        var title = state!="one" ? '密码重置':'生成一次性密码';

        parent.layer.confirm($("#edit-password-pup").html(),
                {title:title,btn: ['确定','取消'], area: ['330px', height]}
            , function(){
                var parentForm = $(window.parent.document).find(".layui-layer-content").find("#edit-password-form");
                var check_auto = parentForm.find("input[name='check1']"),
                    pwd_val = parentForm.find("input[name='pwd']").val(),
                    check_chg = parentForm.find("input[name='check2']");

                var pwd="",is_first_login=1;    
                    if(check_auto.attr("checked")!=undefined){
                       pwd ="";
                    }else{
                        pwd = pwd_val;
                        var lv = 0;
                        if (pwd_val.match(/[A-Z]/g)) {
                            lv++;
                        }
                        if (pwd_val.match(/[a-z]/g)) {
                            lv++;
                        }
                        if (pwd_val.match(/[0-9]/g)) {
                            lv++;
                        }
                        if (pwd_val.length >= 6 && pwd_val.length <= 18) {
                            lv++
                        }
                        if (lv < 4) {
                            parentForm.find("span").show();
                            return false;
                        } else {
                            parentForm.find("span").hide();
                        }
                    }
                    if(check_chg.attr("checked")!=undefined){
                       is_first_login = 1;
                    }else{
                        is_first_login = 0;
                    }
                var data = {pwd:pwd,is_first_login:is_first_login,uid:'<{$v.uid}>',type:type};
                var msg = 
                successFn(data,state);
            })
         parent.is_switch();
    }

    function successFn(data,state){
        var s = state;
        $.ajax({
            url:"?ct=admin&ac=create_pwd",
            data:data,
            type:"get",
            success:function(data){
                if(data.code==0){
                    openSuccess(data.data.pwd,s);
                }else{
                    
                }
            }
        });
        
        //parent.showPassword();
    }
    function openSuccess(data,state){
        title = state!="one" ? '密码重置成功':'一次性密码生成成功';
        parent.layer.confirm($("#success-pup").html(),
            {title:title,btn: ['完成'], area: ['300px', '220px']}
            , function(){
                $(window.parent.document).find(".layui-layer-shade").remove();
                $(window.parent.document).find(".layui-layer").remove();

                parent.layer.close();
            })

        enterInput(data)
    }

    function enterInput(data){
        var parent = $(window.parent.document).find(".layui-layer-content").find("#success");
        var input = parent.find("input");
        input.val(data);
    }
</script>
</body>
</html>
