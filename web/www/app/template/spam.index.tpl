<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0" />
    <title><{$app_name}></title>
    <link href="static/css/bootstrap.min14ed.css?v=3.3.6" rel="stylesheet">
    <link href="static/css/font-awesome.min93e3.css?v=4.4.0" rel="stylesheet">
    <link href="static/css/animate.min.css" rel="stylesheet">
    <link href="static/css/main.css" rel="stylesheet">
    <script src="static/js/jquery.min.js?v=2.1.4"></script>
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
                            <!--<label>分类</label>-->
                            <select name="module" class="form-control">         
                                <{html_options options=$keys}>       
                            </select>
                        </div>
                        <div class="form-group">
                            <!--<label>分类</label>-->
                            <select name="key" class="form-control">         
                                    
                            </select>
                        </div>
                        <div class="form-group">
                            <!--<label>关键字</label>-->
                            <input type='text' name='keyword' class='form-control' value="<{request_em key='keyword'}>" placeholder="请输入spam内容" />
                        </div>
                        <div class="form-group">
                            <button type="button" class="btn btn-white search_btn">搜索</button>
                        </div>
                    </form>
                </div>
                <div class="widget-content render-view">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script src="static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/js/main.js"></script>
<script src="static/js/plugins/layer/layer.min.js"></script>
<script>

function get_keys(module)
{
    $.getJSON('?ct=spam&ac=get_keys&module='+module, function(data){
        var html = '';
        for(key in data)
        {
            html += '<option value="'+key+'">'+data[key]+'</option>';
        }

        $('select[name="key"]').html(html);
    });
}

$(function(){
    var module = $('select[name="module"]').val();
    get_keys(module);

    $('select[name="module"]').change(function(){
        module = $(this).val();
        get_keys(module);
    });

    $('.search_btn').click(function(){
        var key = window['key'] = $('select[name="key"]').val();
        var keyword = $('input[name="keyword"]').val();

        if( !key ) 
        {
            alert('请选择类目');
        }
        else if( !keyword )
        {
            alert('请输入spam内容');
        }

        $.getJSON('?ct=spam&ac=get_data&key='+key+'&keyword='+encodeURIComponent(keyword), function(data){
            console.log(data);
            renderList(data,key,keyword);
        });
    });

    $('body').on('click','.delete',function(){
        var self = this;
        layer.confirm('确定要删除吗？', {
          btn: ['确定','取消'] //按钮
        }, function(index){
            var idx = layer.load(1, {
              shade: [0.1] //0.1透明度的白色背景
            });
            var key = window['key'];
            var keyword = $(self).data('keyword');
            var auto_clear = $('input[name="auto_clear"]:checked').val();
            if( !auto_clear ) auto_clear = 0;

            $.getJSON('?ct=spam&ac=clear_data&key='+key+'&keyword='+encodeURIComponent(keyword)+'&auto_clear='+auto_clear, function(data){
                if(data.code == 1){
                    var form = $('select[name="key"]').closest('form')[0];
                    // form && form.reset();
                    renderList();
                }else {
                    alert(data.msg);
                }

                layer.close(index);
                layer.close(idx);

            });
        }, function(){
         
        })
        
    });
});

function renderList(data,key,keyword){
    var map = {
        'limit': '系统阀值',
        'total': '促发次数',
        'timestamp': '最后保存时间',
        'interval' : '频率',
        'data': '数据'
    }
    var is_empty = true;
    var html = '<table class="table table-bordered"><tr><th width="200px" style="text-align:left">属性</th><th style="text-align:left">值</th></tr>';
    for(var key in data){
        is_empty = false;
        html += '<tr>';
        html += '<td>'+ map[key] +'</td>';
        if(key == 'data') {
            html += '<td>'+JSON.stringify(data[key])+'</td>';
        }else {
            html += '<td>'+ data[key] +'</td>'
        }
        html += '</tr>';
    }
    html += '</table>';
                            
    html += '<button type="button" class="btn btn-danger delete m-t" data-key="'+ key +'" data-keyword="'+keyword+'">删除</button>'

    is_empty && (html = '');
    
    $('.render-view').html(html);

}
</script>

</body>
</html>

