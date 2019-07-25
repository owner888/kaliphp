<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title>PHPCALL</title>
    <link href="static/frame/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="static/frame/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="static/frame/css/animate.min.css" rel="stylesheet">
    <link href="static/frame/css/authentication.css" rel="stylesheet">
    <script src="static/frame/js/jquery.min.js?v=2.1.4"></script>


</head>
<body id="authen">
<div id="content">
    <div class="header">
        <div class="header-inner">
            <div class="pull-left">
                <div class="img">
                    <a href="/"><img src="static/frame/img/logo.svg" alt="" width="50px;"></a>
                    <a href="/">PHPCALL</a>
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
            <div class="verify">
                <p style="margin: 20px auto;"><strong style="color: #000000">请在手机端下载并安装 Google Authenticator 应用</strong></p>
                <div class="img-list">
                    <img src="static/frame/img/authenticator_android.png" width="128" height="128" alt="" />
                    <p>Android手机下载</p>
                </div>

                <div class="img-list">
                    <img src="static/frame/img/authenticator_iphone.png" width="128" height="128" alt="" >
                        <p>iPhone手机下载</p>
                    </div>

                    <p style="margin: 20px auto;"></p>
                    <p style="margin: 20px auto;"><strong style="color: #000000">安装完成后点击下一步进入绑定页面（如已安装，直接进入下一步）</strong></p>
                </div>

                <a href="?ct=otp_enable&ac=bind" class="next btn">下一步</a>
            </div>
        </article>
        <footer>
            <div class="" style="margin-top: 100px;text-align: center">
                <p>傲创网络科技 ©
                    <a href="#" target="_blank">ultron.com</a>
                </p>
            </div>
        </footer>
    </div>

    <script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
    <script src="static/frame/js/validform.js"></script>
    <script src="static/frame/js/newvalidform.js"></script>
</body>
</html>
