<!DOCTYPE html>
<html>
<head>
    <{include file='common/header.tpl'}>
</head>
<body>
<div id="content">
    <div class="container-fluid">
        <div class="row">
            <div class="widget-box">
                <form class="form-horizontal" id="validateForm" novalidate="novalidate" action="" method="POST">

                    <div class="widget-content">
                        <h5>你好：<{$info.realname}> ( <{$info.username}> )</h5>
                    </div>

                    <div class="widget-title">
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <h5>用户信息</h5>
                    </div>
                    <div class="widget-content">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户名 :</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                <{$info.username}>
                                </p>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">用户组 :</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                <{if !empty($info.groupname)}>
                                <{$info.groupname}>
                                <{else}>
                                <font color="red">不属于任何组</font>
                                <{/if}>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="widget-title">
                        <!--<span class="icon"> <i class="fa fa-align-justify"></i> </span>-->
                        <h5>权限管理</h5>
                    </div>
                    <div class="widget-content">
                        <{if '*' == $info.purviews}>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">权限:</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <!--此用户拥有至高无上的权限 ^_^!-->
                                    您法力无边 ^_^!
                                </p>
                            </div>
                        </div>
                        <{else}>

                        <div id="checkbox-group"></div>

                        <{/if}>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<{if '*' != $info.purviews}>
<script>
    var PurviewData = <{$purviews}>;
</script>
<{/if}>
<{include file='common/footer.tpl'}>
<script>
    function purviewFn(){
            if("undefined" != typeof PurviewData){
                function getData(index) {  //分步循环
                    if (PurviewData[index].children) {
                        $("#side-menu").empty();
                        var sideStr = "";
                        var arr = PurviewData[index].children;

                        function render(arr, level) {
                            var level = level || 0;
                            level++;
                            var i = 0, len = arr.length;
                            if (level == 1) {
                                var str = '<ul class="checkbox-wrap checkbox-item">'
                            } else {
                                str = '<ul class="item-second-level col-sm-10">'
                            }
                            for (; i < len; i++) {
                                if (arr[i].children && arr[i].children.length > 0) {
                                    str += '<li class="nav' + arr[i].id + ' block"><label class="has-child pull-left m-r-sm a"  data-index="' + arr[i].id + '"> ' + arr[i].name + '</label>';
                                    str += render(arr[i].children, level);
                                } else {
                                    str += '<li class="nav' + arr[i].id + ' pull-left"><label class="m-r label label-info " style="margin-top:-3px;" data-index="' + arr[i].id + '"> ' + arr[i].name + '</label>';
                                }
                                str += '</li>';
                            }
                            if (level == 1) {
                                str += '</ul>';
                            } else {
                                str += '</ul><div class="hr-line-dashed"></div>';
                            }
                            return str;
                        }
                        $("body").find(".form-group-item").eq(index).children(".col-sm-10").html(render(arr))
                    } else {
                        return false
                    }
                }
                var str = "";
                for(var i = 0;i<PurviewData.length;i++){
                    str+='<div class="form-group form-group-item" data-id="'+PurviewData[i].id+'"><label class="col-sm-2 control-label">'+PurviewData[i].name+':</label><div class="col-sm-10"></div></div>'
                }
                $("#checkbox-group").html(str);

                $("body").find(".form-group-item").each(function(i) {
                    getData(i)
                })
            }

        }

    purviewFn();
</script>
</body>
</html>
