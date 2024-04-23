<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>
<body>
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
<{include file='common/footer.tpl'}>
</body>
</html>