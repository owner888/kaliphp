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
                        <i class="iconfont icon-step2 active"></i>
                        <span></span>
                    </div>
                    <div class="back">安装应用</div>
                </li>
                <li>
                    <div>
                        <i class="iconfont icon-step1 active"></i>
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
            <div class="verify">
                <p style="margin:20px auto;"><strong style="color: #000000">使用手机 Google Authenticator 应用扫描以下二维码，获取6位验证码</strong></p>
                <div>
                    <img src="<{$imagedata}>" width="180" height="180" alt="" />
                </div>

                <p style="margin: 20px auto;"></p>
                <form class="" role="form" method="post" action="">
                    <{form_token}>
                    <div class="form-group">
                        <input type="text" class="form-control" name="otp_code" placeholder="6位数字" style="width:180px;">
                    </div>
                    <button type="submit" class="next btn" style="width:180px;">下一步</button>
                    <{if $err_msg}>
                    <p class="red-fonts">* <{$err_msg}></p>
                    <{/if}>
                </form>
            </div>
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
