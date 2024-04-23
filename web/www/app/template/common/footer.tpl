<script src="static/js/jquery.min.js?v=2.1.4"></script>
<script src="static/js/layui.js"></script>
<script src="static/js/bootstrap.min.js?v=3.3.6"></script>
<script src="static/js/highcharts.js"></script>
<script src="static/js/js.cookie.min.js"></script>
<script src="static/js/plugins/table/tableModify.js"></script>
<script src="static/js/jquery.json-editor.min.js"></script>
<script src="static/js/plugins/datapicker/bootstrap-datetimepicker.min.js"></script>
<script src="static/js/plugins/datapicker/bootstrap-datetimepicker.zh-CN.js"></script>
<script src="static/js/validform.js"></script>
<script src="static/js/newvalidform.js"></script>
<script src="static/js/Sortable.min.js"></script>
<script src="static/js/select2.min.js"></script>
<script src="static/editor.md/editormd.min.js"></script>
<script src="static/webuploader/webuploader.min.js"></script>
<script src="static/js/webuploader.own.js<{$clear_cache}>"></script>
<script src="static/js/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="static/js/main.js"></script>

<script>
$(function(){
    // 这里做成排序闭包函数，只需要 html 加id 和 clss 就可以拷到其他 form 去用
    ;(function(win, $){
        const qs = win.qs;
        if(!qs){
            return layui.msg('qs is null');
        }
        // 从 url 拿到两个参数，找到排序那一列，给 icon 做样式
        const { order_desc = '', order_name = '' } = qs;
        if(!!order_desc && !!order_name){
            $('.list-sort[rel="'+order_name+'"]').find('.sort').removeClass().addClass(`fa fa-sort-${order_desc} sort`);
        }
        $(".list-sort").on('click', function() {
            let relValue = $(this).attr('rel');
            let seq = 'desc';
            if(qs.order_name === relValue){
                seq = !qs.order_desc ? 'desc' : (qs.order_desc === 'desc') ? 'asc' : (qs.order_desc === 'asc') ? '' : 'desc';
            }
            $('#order_name').val(seq === '' ? '' : relValue);
            $('#order_desc').val(seq);

            // 表单提交
            $('#search-form').submit();
        })
    })(window, jQuery);

    layui.use('layer', function(){
        const layer = layui.layer;
        
        layer.photos({
            photos: '.layer-photos',
            anim: 5
        });
    });
})
</script>