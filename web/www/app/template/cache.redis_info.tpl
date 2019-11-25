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

                <div class="widget-content p-b-none">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group">
                            <!--<label>关键字</label>-->
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="请输入Key关键字" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white">搜索</button>
                        </div>
                    </form>
                </div>
                <div class="widget-content">

                    <form action="" method="POST">
                        <{form_token}>
                        <table class="table table-bordered table-hover table-agl-c">
                            <thead>
                                <tr>
                                    <th> Key </th>
                                    <th> Value </th>
                                </tr>
                            </thead>
                            <tbody>
                                <{foreach from=$list key=k item=v}>
                                <tr>
                                    <td> <{$k}> </td>
                                    <td> <{$v}> </td>
                                </tr>
                                <{foreachelse}>
                                <tr>
                                    <td colspan="2">暂无Redis</td>
                                </tr>
                                <{/foreach}>
                                <tr>
                                    <td colspan="2">
                                        <div class="fl">
                                        </div>
                                        <div class="fr">
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
<script src="static/frame/js/main.js"></script>
</body>
</html>

