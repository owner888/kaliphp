<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>
<body>
<div id="content">
    <div class="container-fluid">
        <div class="row">
            <!--<div class="btn-outline-wrap">-->
                <!--<a href="javascript:history.back(-1)" class="btn btn-success btn-outline"><i class="fa fa-chevron-left"></i>返回</a>-->
                <!--<a href="?ct=config&ac=index" class="btn btn-success btn-outline"><i class="fa fa-bars"></i>配置列表</a>-->
                <!--<a href="?ct=config&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i>配置添加</a>-->
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
                            <label class="col-sm-2 control-label"><code>*</code> 说明标题:</label>
                            <div class="col-sm-10">
                                <input type="text" name='title' class="form-control" value="<{$v.title}>" datatype="*" nullmsg="请输入说明标题" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">分组:</label>
                            <div class="col-sm-10">
                                <select name="group" class="form-control">         
                                    <{html_options options=$options selected=$v.group}>       
                                </select>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 变量名:</label>
                            <div class="col-sm-10">
                                <input name="name" type="text" class="form-control" value="<{$v.name}>" datatype="*" nullmsg="请输入变量名" />
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">变量类型:</label>
                            <div class="col-sm-10">
                                <div class="radio" id="group-radio">
                                    <label><input type='radio' name='type' value='string' <{if $v.type=='string'}>checked<{/if}> /> 字符串</label>
                                    <label><input type='radio' name='type' value='number' <{if $v.type=='number'}>checked<{/if}> /> 数字</label>
                                    <label><input type='radio' name='type' value='text' <{if $v.type=='text'}>checked<{/if}> /> 多行文本</label>
                                    <label><input type='radio' name='type' value='bool'<{if $v.type=='bool'}>checked<{/if}> /> Bool(布尔变量)</label>
                                    <label><input type='radio' name='type' value='json'<{if $v.type=='json'}>checked<{/if}> /> JSON格式</label>
                                </div>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"><code>*</code> 变量值:</label>
                            <div class="col-sm-10" id="dynamic-varible">
                                <{if $v.type=='bool'}>
                                <div class="radio">
                                    <label><input type='radio' name='value' value='1' <{if $v.value=='1'}>checked<{/if}> /> 是</label>
                                    <label><input type='radio' name='value' value='0' <{if $v.value=='0'}>checked<{/if}> /> 否</label>
                                </div>
                                <{else}>
                                <textarea type='input' name='value' class="form-control" datatype="*" nullmsg="请输入变量值"><{$v.value}></textarea>
                                <{/if}>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">排序:</label>
                            <div class="col-sm-10">
                                <input type="text" name='sort' class="form-control" value="<{$v.sort}>" style="width:50px;" />
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
<{include file='common/footer.tpl'}>
<script>
    $(function(){
        var tpl_textarea = `<textarea type='input' name='value' class="form-control" datatype="*" nullmsg="请输入变量值"><{$v.value}></textarea>`;
        var tpl_raido = `<div class="radio"><label><input type='radio' name='value' value='1' <{if $v.value=='1'}>checked<{/if}> /> 是</label><label><input type='radio' name='value' value='0' <{if $v.value=='0'}>checked<{/if}> /> 否</label></div>`;
    
        $('#group-radio input').on('click', function(){
            var type = $(this).val();
            switch(type){
                case 'bool':
                    // 单选框
                    $('#dynamic-varible').html(tpl_raido);
                    break;
                case 'json':
                    // json.view 编辑器
                    $('#dynamic-varible').html(tpl_textarea);
                    break;
                default:
                    $('#dynamic-varible').html(tpl_textarea);
                    break;
            }
        })
    })
    </script>
</body>
</html>
