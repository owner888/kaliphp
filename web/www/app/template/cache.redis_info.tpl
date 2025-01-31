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

                <div class="widget-content p-b-none">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group">
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="请输入Key关键字" />
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
<{include file='common/footer.tpl'}>
</body>
</html>