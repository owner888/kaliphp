<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>
<body id="authen">
<div id="content">
    <div class="header">
        <div class="header-inner">
            <div class="pull-left">
                <div class="img">
                    <a href="/"><img src="static/img/logo.svg" alt="" width="50px;"></a>
                </div>
            </div>
        </div>
    </div>
    <article class="install-app">
        <div class="clearfix">
            <ul class="change-color">
                <li>
                    <div>
                        <i class="iconfont icon-step active"></i>
                        <span></span>
                    </div>
                    <div class="back">验证身份</div>
                </li>
                <li>
                    <div>
                        <i class="iconfont icon-step2 "></i>
                        <span></span>
                    </div>
                    <div class="back">安装应用</div>
                </li>
                <li>
                    <div>
                        <i class="iconfont icon-step1"></i>
                        <span></span>
                    </div>
                    <div class="back">绑定MFA</div>
                </li>
                <li>
                    <div>
                        <i class="iconfont icon-duigou"></i>
                    </div>
                    <div>完成</div>
                </li>
            </ul>
        </div>
        <div>
            <div class="verify">安全令牌验证&nbsp;&nbsp;账户&nbsp;<span style="color:red"><{$username}></span>&nbsp;&nbsp;请按照以下步骤完成绑定操作</div>
            <div class="line"></div>
            <form class="form-horizontal" role="form" method="post" action="">
                <{form_token}>
                <div class="form-group" style="margin-top:30px">
                    <input type="text" class="form-control" name="username" value="<{$username}>" placeholder="请输入账号" datatype="*" readonly="readonly" />
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="请输入密码" datatype="password" errmsg="密码必须包含大小写字母，数字" />
                </div>
                <button type="submit" class="next btn">下一步</button>
                <{if $err_msg}>
                <p class="red-fonts">* <{$err_msg}></p>
                <{/if}>
            </form>

        </div>
    </article>
    <footer>
        <div class="" style="margin-top: 100px;text-align: center">
            <p>KaliPHP ©
                <a href="#" target="_blank">doc.kaliphp.com</a>
            </p>
        </div>
    </footer>
</div>
<{include file='common/footer.tpl'}>
</body>
</html>
