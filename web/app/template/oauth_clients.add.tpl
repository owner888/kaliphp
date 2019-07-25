<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
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
            <!--<div class="btn-outline-wrap">-->
                <!--<a href="javascript:history.back(-1)" class="btn btn-success btn-outline"><i class="fa fa-chevron-left"></i><{lang key='common_back'}></a>-->
                <!--<a href="?ct=category&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>分类列表</a>-->
                <!--<a href="?ct=category&ac=add" class="btn btn-info"><i class="fa fa-plus-circle"></i>分类添加</a>-->
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
                            <label class="col-sm-2 control-label"><code>*</code> 应用名称:</label>
                            <div class="col-sm-10">
                                <input name="name" type="text" class="form-control" datatype="*" nullmsg="请输入应用名称"/>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 应用官网:</label>
                            <div class="col-sm-10">
                                <input name="website" type="text" class="form-control" datatype="*" nullmsg="请输入应用官网" value="http://" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> client_id:</label>
                            <div class="col-sm-10">
                                <input name="client_id" type="text" class="form-control" value="<{$client_id}>" readonly />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> client_secret:</label>
                            <div class="col-sm-10">
                                <input name="client_secret" type="text" class="form-control" value="<{$client_secret}>" readonly />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 应用简介:</label>
                            <div class="col-sm-10">
                                <input name="desc" type="text" class="form-control" datatype="*" nullmsg="请输入应用简介"/>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 授权回调页:</label>
                            <div class="col-sm-10">
                                <input name="redirect_uri" type="text" class="form-control" datatype="*" nullmsg="请输入安全域名" value="http://"/>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"> 取消授权回调页:</label>
                            <div class="col-sm-10">
                                <input name="cancel_uri" type="text" class="form-control" value="http://"/>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 域名绑定:</label>
                            <div class="col-sm-10">
                                <input name="domain" type="text" class="form-control" datatype="*" nullmsg="请输入域名"/>
                                <p class="help-block m-b-none m-t-xs"><i class="fa fa-info-circle"></i> 应用安全选项，绑定域名后其他域名无法使用你的client_id </p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 信任IP:</label>
                            <div class="col-sm-10">
                                <input name="ip" type="text" class="form-control" datatype="*" nullmsg="请输入IP"/>
                                <p class="help-block m-b-none m-t-xs"><i class="fa fa-info-circle"></i> 应用安全选项，只有在此信任IP列表中的服务器，才可访问平台OpenAPI，多个IP以逗号分隔 </p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"> 授权域(scope):</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <{foreach from=$scopes item='v'}>
                                    <label>
                                        <input type="checkbox" name="scope[]" value="<{$v.scope}>" <{if $v.is_default}>checked<{/if}> /> <{$v.name}>
                                    </label>
                                    <{/foreach}>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"> 授权方式（grant_type）:</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="grant_types[]" value="authorization_code" checked /> 授权码
                                    </label>
                                    <label>
                                        <input type="checkbox" name="grant_types[]" value="refresh_token" checked /> 刷新Token
                                    </label>
                                    <label>
                                        <input type="checkbox" name="grant_types[]" value="password" /> 用户口令
                                    </label>
                                    <label>
                                        <input type="checkbox" name="grant_types[]" value="client_credentials" /> 应用授权
                                    </label>
                                </div>
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

<script src="static/frame/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/frame/js/validform.js"></script>
<script src="static/frame/js/newvalidform.js"></script>
<script src="static/frame/js/main.js"></script>

</body>
</html>
