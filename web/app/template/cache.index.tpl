<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" category="width=device-width, initial-scale=1.0">
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
                    <table class="table table-bordered table-hover table-agl-c">
                        <thead>
                            <tr>
                                <th> 缓存内容 </th>
                                <th> 管理 </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> 网址导航 </td>
                                <td>
                                    <a onclick="plt.confirmAction(event)" data-href="?ct=cache&ac=clear&type=guonei" data-title="确定清除缓存"
                                        data-tipmsg="是否确定清除缓存" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>清除缓存</a>
                                </td>
                            </tr>
                            <tr>
                                <td> 网址站群 </td>
                                <td>
                                    <a onclick="plt.confirmAction(event)" data-href="?ct=cache&ac=clear&type=site" data-title="确定清除缓存"
                                        data-tipmsg="是否确定清除缓存" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i>清除缓存</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/main.js"></script>
</body>
</html>

