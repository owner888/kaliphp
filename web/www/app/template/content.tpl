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

                <div class="widget-content p-b-none">
                    <div class="btn-outline-wrap">
                        <a href="?ct=content&ac=add" class="btn btn-info btn-outline"><i class="fa fa-plus-circle"></i><{lang key='common_add'}></a>
                        <!--<a class="btn btn-outline btn-danger"><i class="fa fa-trash-o"></i><{lang key='common_trash'}></a>-->
                    </div>
                </div>

                <div class="widget-content p-b-none">
                    <form class="form-inline" action="" method="GET">
                        <input type="hidden" name="ct" value="<{request_em key='ct'}>" />
                        <input type="hidden" name="ac" value="<{request_em key='ac'}>" />
                        <div class="form-group">
                            <!--<label><{lang key='common_category'}></label>-->
                            <select name="catid" class="form-control">         
                                <{html_options options=$options selected=$catid}>       
                            </select>
                        </div>
                        <div class="form-group">
                            <!--<label>关键字</label>-->
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="<{lang key='content_category_search_txt'}>" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-white"><{lang key='common_search'}></button>
                        </div>
                    </form>
                </div>
                <div class="widget-content">
                    <table class="table table-bordered table-hover table-agl-c with-check">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th> <{lang key='common_name'}> </th>
                                <th> <{lang key='common_image'}> </th>
                                <th> <{lang key='common_time_add'}> </th>
                                <th> <{lang key='common_time_edit'}> </th>
                                <th style="width:210px"> <{lang key='common_manage'}> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <{foreach from=$list item=v}>
                            <tr>
                                <td> <{$v.id}> </td>
                                <td> <{$v.name}> </td>
                                <td> <img src="<{$v.imageurl}>" width="100px" height="100px" /> </td>
                                <td> <{$v.created_at}> </td>
                                <td> <{$v.updated_at}> </td>
                                <td> 
                                    <a href="?ct=content&ac=info&id=<{$v.id}>" class="btn btn-success btn-xs"><i class="fa fa-search"></i><{lang key='common_view'}></a>
                                    <a href="?ct=content&ac=edit&id=<{$v.id}>" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i><{lang key='common_edit'}></a>
                                    <a onclick="plt.confirmAction(event)" data-href="?ct=content&ac=del&ids[]=<{$v.id}>" data-title="<{lang key='common_system_hint'}>" data-tipmsg="<{lang key='common_sure_delete'}>" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i><{lang key='common_delete'}></a>
                                </td>
                            </tr>
                            <{foreachelse}>
                            <tr>
                                <td colspan="6"><{lang key='common_no_record'}></td>
                            </tr>
                            <{/foreach}>
                            <tr>
                                <td colspan="6">
                                    <div class="fl">
                                        <a data-href="?ct=content&ac=del" class="btn btn-danger" onclick="plt.subform(event,'child')"
                                            data-title="<{lang key='common_system_hint'}>"
                                            data-tipmsg="<{lang key='common_sure_batch_delete'}>" data-errmsg="<{lang key='common_please_select_records'}>"
                                        ><i class="fa fa-trash-o"></i><{lang key='common_batch_delete'}></a>
                                        <a data-href="?ct=content&ac=edit_batch" class="btn btn-primary" onclick="plt.subform(event,'child')" data-title="<{lang key='common_system_hint'}>"
                                            data-tipmsg="<{lang key='common_sure_batch_edit'}>" data-errmsg="<{lang key='common_please_select_records'}>">
                                        <i class="fa fa-edit"></i><{lang key='common_batch_edit'}></a>
                                    </div>
                                    <div class="fr">
                                        <{$pages}>
                                    </div>
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

