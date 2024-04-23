<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>
<body>
    <div id="loginbox">            
        <div class="control-group normal_text"><h3><{$app_name}></h3></div>

        <div class="control-group">
            <div class="controls">
                <div class="main_input_box">
                    <div class="login-error">
                        账号锁定，解锁时间：<{$unlock_time|date_format:"%Y年%m月%d日 %H:%M:%S"}>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
