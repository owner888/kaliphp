<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>
<body>
<style>
    .inline-control {
        padding: 0 15px;
        height: 27px;
        border: 1px solid #ddd !important;
    }
</style>
<div id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="widget-box">
                <form class="form-horizontal" id="validateForm" novalidate="novalidate" action="" method="POST">
                    <{form_token}>
                    <div class="widget-title">
                        <span class="icon"><a href="javascript:history.back(-1)"><i class="fa fa-chevron-left"></i>返回</a></span>
                    </div>

                    <div class="widget-content">
                        <input name="id" type="hidden" value="<{$data.id|default:0}>"  />
                        <div class="form-group">
                            <label class="col-sm-2 control-label">APP系统:</label>
                            <div class="col-sm-10">
                                <select name="os" class="form-control">         
                                    <{html_options options=$os_options selected=$data.os}>       
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">包名:</label>
                            <div class="col-sm-10">
                                <input name="bound_id" type="text" class="form-control" value="<{$data.bound_id|default:''}>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">最新版本号:</label>
                            <div class="col-sm-10">
                                <input name="version" type="text" class="form-control" value="<{$data.version|default:''}>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">下载地址:</label>
                            <div class="col-sm-10">
                                <input name="app_url" type="text" class="form-control" value="<{$data.app_url|default:''}>" />
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> Play Store 和 App Store 是应用内更新的，只需要填外部更新地址即可</span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">MD5:</label>
                            <div class="col-sm-10">
                                <input name="md5" type="text" class="form-control" value="<{$data.md5|default:''}>"  maxlength="32" />
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 不填则客户端不进行判断</span>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">更新规则:</label>
                            <div class="col-sm-10 rulesbox">
                                <!-- 没有更新规则说明是新增 -->
                                <{if empty($data.rules)}>
                                <div class="rulesbox_first">
                                    <div class="control-rules" style="margin-bottom:15px;">
                                        APP版本号：
                                        <select name="rules[condition][]" class="inline-control">
                                            <{foreach $condition_maps as $key => $condition}>
                                            <option value="<{$key}>"><{$condition}></option>
                                            <{/foreach}>
                                        </select>
                                        <input name="rules[version][]" type="text" class="inline-control"/>

                                        <select name="rules[update_mode][]" class="inline-control">
                                            <{foreach $update_mode as $key => $val}>
                                            <option value="<{$key}>"><{$val}></option>
                                            <{/foreach}>
                                        </select>
                                        <button type="button" class="btn btn-danger btn-xs">删除规则</button>
                                    </div>
                                </div>
                                <{else}>
                                <div class="rulesbox_last">
                                    <{foreach $data.rules as $k => $v}>
                                    <div class="control-rules" style="margin-bottom:15px;">
                                        APP版本号：
                                        <select name="rules[condition][]" class="inline-control">
                                            <{foreach $condition_maps as $key => $condition}>
                                            <option value="<{$key}>" <{if $v['condition'] == $key}> selected <{/if}>><{$condition}></option>
                                            <{/foreach}>
                                        </select>
                                        <input name="rules[version][]" type="text" value="<{$v['version']|default:''}>" />

                                        <select name="rules[update_mode][]" class="inline-control">
                                            <{foreach $update_mode as $key => $val}>
                                            <option value="<{$key}>" <{if $v['update_mode'] == $key}> selected <{/if}>><{$val}></option>
                                            <{/foreach}>
                                        </select>
                                        <button type="button" class="btn btn-danger btn-xs delete_rules">删除规则</button>
                                    </div>
                                    <{/foreach}>
                                </div>
                                <{/if}>
                                <button type="button" class="btn btn-primary btn-xs add_rules">添加规则</button>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">中文描述:</label>
                            <div class="col-sm-10">
                                <textarea name="tips[cn_remark]" rows="6" class="form-control"><{$data.tips.cn_remark|default:''}></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">英文描述:</label>
                            <div class="col-sm-10">
                                <textarea name="tips[en_remark]" rows="6" class="form-control"><{$data.tips.en_remark|default:''}></textarea>
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
    // 新增规则
    $('body').on('click','.add_rules',function(){
        var one = '<div class="control-rules" style="margin-bottom:15px;">'+
                        'APP版本号： '+ 
                        '<select name="rules[condition][]" class="inline-control">'+
                            '<{foreach $condition_maps as $key => $condition}>'+
                            '<option value="<{$key}>"><{$condition}></option>'+
                            '<{/foreach}>'+
                        '</select> '+
                        '<input name="rules[version][]" type="text" class="inline-control"/> '+
                        '<select name="rules[update_mode][]" class="inline-control">'+
                            '<{foreach $update_mode as $key => $val}>'+
                            '<option value="<{$key}>"><{$val}></option>'+
                            '<{/foreach}>'+
                        '</select> '+
                        '<button type="button" class="btn btn-danger btn-xs delete_rules">删除规则</button>'+
                    '</div>';

        var two = '<div class="control-rules" style="margin-bottom:15px;">'+
                        'APP版本号： '+
                        '<select name="rules[condition][]" class="inline-control">'+
                            '<{foreach $condition_maps as $key => $condition}> '+
                            '<option value="<{$key}>"><{$condition}></option> '+
                            '<{/foreach}> '+
                        '</select> '+
                        '<input name="rules[version][]" type="text" value="" /> '+
                        '<select name="rules[update_mode][]" class="inline-control"> '+
                            '<{foreach $update_mode as $key => $val}> '+
                            '<option value="<{$key}>"><{$val}></option> '+
                            '<{/foreach}> '+
                        '</select> '+
                        '<button type="button" class="btn btn-danger btn-xs delete_rules">删除规则</button> '+
                    '</div>';

        if($(this).parents('.rulesbox').find('.rulesbox_first').length > 0){
            $(this).parents('.rulesbox').find('.rulesbox_first').append(one);
        }else if($(this).parents('.rulesbox').find('.rulesbox_last').length > 0){
            $(this).parents('.rulesbox').find('.rulesbox_last').append(two);
        }
    });
    // 删除规则
    $('body').on('click','.delete_rules',function(){
        $(this).parents('.control-rules').remove();
    });
</script>
</body>
</html>
