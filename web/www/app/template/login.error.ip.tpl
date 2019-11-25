<!DOCTYPE html>
<html lang="en">
    <head>
        <title><{$title}></title><meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="stylesheet" href="static/frame/css/bootstrap.min.css" />
        <link rel="stylesheet" href="static/frame/css/bootstrap-responsive.min.css" />
        <link rel="stylesheet" href="static/font-awesome/css/font-awesome.css" />
        <link rel="stylesheet" href="static/font-awesome/css/font-google.css" />
        <link rel="stylesheet" href="static/frame/css/matrix-login.css?fff" />
    </head>
    <body>
        <div id="loginbox">            
                <div class="control-group normal_text"><h3><{$title}></h3></div>

                <div class="control-group">
                    <div class="controls">
                        <div class="main_input_box">
                            <div class="login-error">
                                IP锁定，解锁时间：<{$unlock_time|date_format:"%Y年%m月%d日 %H:%M:%S"}>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        <script src="static/frame/js/jquery.min.js"></script>  
    </body>

</html>
